<?php

/*
 * This file is part of the Jblab PasswordValidatorBundle package.
 * Copyright (c) 2023-2025 Jblab <https://jblab.io/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jblab\PasswordValidatorBundle;

use Jblab\PasswordValidatorBundle\Exception\ConfigurationException;
use Jblab\PasswordValidatorBundle\Exception\PasswordExcludedCharacterException;
use Jblab\PasswordValidatorBundle\Exception\PasswordLowercaseException;
use Jblab\PasswordValidatorBundle\Exception\PasswordMaximumLengthException;
use Jblab\PasswordValidatorBundle\Exception\PasswordMinimumLengthException;
use Jblab\PasswordValidatorBundle\Exception\PasswordNumberException;
use Jblab\PasswordValidatorBundle\Exception\PasswordSpecialCharacterException;
use Jblab\PasswordValidatorBundle\Exception\PasswordUppercaseException;

class PasswordValidator implements PasswordValidatorInterface
{
    protected int $minimumLength;
    protected ?int $maximumLength;
    protected bool $requireSpecialCharacter;
    protected bool $requireUppercase;
    protected bool $requireLowercase;
    protected bool $requireNumber;
    protected ?string $specialCharacterSet;
    protected ?string $excludedCharacterSet;

    /**
     * @throws ConfigurationException
     */
    public function __construct(
        bool $requireSpecialCharacter,
        bool $requireUppercase,
        bool $requireLowercase,
        bool $requireNumber,
        ?string $specialCharacterSet = null,
        int $minimumLength = 1,
        ?int $maximumLength = null,
        ?string $excludedCharacterSet = null,
    ) {
        $this->minimumLength           = $minimumLength;
        $this->maximumLength           = $maximumLength;
        $this->requireSpecialCharacter = $requireSpecialCharacter;
        $this->requireUppercase        = $requireUppercase;
        $this->requireLowercase        = $requireLowercase;
        $this->requireNumber           = $requireNumber;
        $this->specialCharacterSet     = $specialCharacterSet;
        $this->excludedCharacterSet    = $excludedCharacterSet;

        $this->validateConfiguration();
    }

    /**
     * @param bool $throwException if true the validator will throw an exception instead
     *                             of returning false when password isn't valid
     *
     * @throws PasswordExcludedCharacterException
     * @throws PasswordLowercaseException
     * @throws PasswordMaximumLengthException
     * @throws PasswordMinimumLengthException
     * @throws PasswordNumberException
     * @throws PasswordSpecialCharacterException
     * @throws PasswordUppercaseException
     */
    public function validate(string $password, bool $throwException = false): bool
    {
        if (!$this->validateMaximumLength($password, $throwException)) {
            return false;
        }

        if (!$this->validateMinimumLength($password, $throwException)) {
            return false;
        }

        if (!$this->validateLowercaseLetter($password, $throwException)) {
            return false;
        }

        if (!$this->validateUppercaseLetter($password, $throwException)) {
            return false;
        }

        if (!$this->validateNumber($password, $throwException)) {
            return false;
        }

        if (!$this->validateSpecialCharacters($password, $throwException)) {
            return false;
        }

        if (!$this->validateExcludedCharacters($password, $throwException)) {
            return false;
        }

        return true;
    }

    /**
     * Returns a regular expression that can be used to validate
     * a password based on the current bundle configuration.
     */
    public function getRegEx(): string
    {
        $regEx = '^';

        if ($this->requireLowercase) {
            $regEx .= '(?=.*[a-z])';
        }

        if ($this->requireUppercase) {
            $regEx .= '(?=.*[A-Z])';
        }

        if ($this->requireNumber) {
            $regEx .= '(?=.*[0-9])';
        }

        if ($this->requireSpecialCharacter) {
            $regEx .= sprintf('(?=.*[%s])', $this->escapeSpecialCharacters($this->specialCharacterSet));
        }

        if ($this->excludedCharacterSet) {
            $regEx .= sprintf('(?!.*[%s])', $this->escapeSpecialCharacters($this->excludedCharacterSet));
        }

        if ($this->minimumLength && $this->maximumLength) {
            $regEx .= sprintf('.{%d,%d}', $this->minimumLength, $this->maximumLength);
        } elseif ($this->minimumLength) {
            $regEx .= sprintf('.{%d,}', $this->minimumLength);
        } elseif ($this->maximumLength) {
            $regEx .= sprintf('.{0,%d}', $this->maximumLength);
        }

        $regEx .= '$';

        return $regEx;
    }

    /**
     * @throws PasswordMaximumLengthException
     */
    protected function validateMaximumLength(string $password, bool $throwException): bool
    {
        if (null !== $this->maximumLength && $this->maximumLength < strlen($password)) {
            if ($throwException) {
                throw new PasswordMaximumLengthException(
                    sprintf('Password must be %d or less characters long.', $this->maximumLength)
                );
            }

            return false;
        }

        return true;
    }

    /**
     * @throws PasswordMinimumLengthException
     */
    protected function validateMinimumLength(string $password, bool $throwException): bool
    {
        if ($this->minimumLength > strlen($password)) {
            if ($throwException) {
                throw new PasswordMinimumLengthException(
                    sprintf('Password must be at least %d characters long.', $this->minimumLength)
                );
            }

            return false;
        }

        return true;
    }

    /**
     * @throws PasswordLowercaseException
     */
    protected function validateLowercaseLetter(string $password, bool $throwException): bool
    {
        if ($this->requireLowercase) {
            if (!preg_match('/[a-z]+/', $password)) {
                if ($throwException) {
                    throw new PasswordLowercaseException('Password must contain at least one lowercase letter.');
                }

                return false;
            }
        }

        return true;
    }

    /**
     * @throws PasswordUppercaseException
     */
    protected function validateUppercaseLetter(string $password, bool $throwException): bool
    {
        if ($this->requireUppercase) {
            if (!preg_match('/[A-Z]+/', $password)) {
                if ($throwException) {
                    throw new PasswordUppercaseException('Password must contain at least one uppercase letter.');
                }

                return false;
            }
        }

        return true;
    }

    /**
     * @throws PasswordNumberException
     */
    protected function validateNumber(string $password, bool $throwException): bool
    {
        if ($this->requireNumber) {
            if (!preg_match('/\d+/', $password)) {
                if ($throwException) {
                    throw new PasswordNumberException('Password must contain at least one number.');
                }

                return false;
            }
        }

        return true;
    }

    /**
     * @throws PasswordSpecialCharacterException
     */
    protected function validateSpecialCharacters(string $password, bool $throwException): bool
    {
        if ($this->requireSpecialCharacter) {
            $pattern = sprintf('/[%s]+/', $this->escapeSpecialCharacters($this->specialCharacterSet));
            if (!preg_match($pattern, $password)) {
                if ($throwException) {
                    throw new PasswordSpecialCharacterException(
                        sprintf(
                            'Password must contain at least one special character from this list "%s".',
                            $this->specialCharacterSet
                        )
                    );
                }

                return false;
            }
        }

        return true;
    }

    protected function escapeSpecialCharacters(?string $characters): string
    {
        $needEscape = ['[', ']', '(', ')', '{', '}', '*', '+', '?', '|', '^', '$', '.', '\\', '/', '=', '-'];
        $characters = array_unique(str_split((string) $characters));
        $escaped    = [];

        foreach ($characters as $character) {
            $escaped[] = in_array($character, $needEscape) ? '\\' . $character : $character;
        }

        return implode('', $escaped);
    }

    /**
     * @throws PasswordExcludedCharacterException
     */
    protected function validateExcludedCharacters(string $password, bool $throwException): bool
    {
        if ($this->excludedCharacterSet) {
            $pattern = sprintf('/[%s]+/', $this->escapeSpecialCharacters($this->excludedCharacterSet));
            if (preg_match($pattern, $password)) {
                if ($throwException) {
                    throw new PasswordExcludedCharacterException(
                        sprintf('Password may not contain any of these characters "%s".', $this->excludedCharacterSet)
                    );
                }

                return false;
            }
        }

        return true;
    }

    /**
     * @throws ConfigurationException
     */
    private function validateConfiguration(): void
    {
        if ($this->minimumLength < 1) {
            throw new ConfigurationException('Invalid minimum length provided, must be at least 1.');
        }

        if (null !== $this->maximumLength && $this->maximumLength < 1) {
            throw new ConfigurationException('Password maximum length must be 1 or greater if provided.');
        }

        if (null !== $this->maximumLength && $this->maximumLength < $this->minimumLength) {
            throw new ConfigurationException('Maximum password length can\'t be less than minimum password length');
        }

        if ('' === $this->specialCharacterSet && $this->requireSpecialCharacter) {
            throw new ConfigurationException('Special character is required but character set is empty.');
        }
    }
}
