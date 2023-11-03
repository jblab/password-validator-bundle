<?php

namespace Jblab\PasswordValidatorBundle;

use Jblab\PasswordValidatorBundle\Exception\ConfigurationException;
use Jblab\PasswordValidatorBundle\Exception\PasswordExcludedCharacterException;
use Jblab\PasswordValidatorBundle\Exception\PasswordLowercaseException;
use Jblab\PasswordValidatorBundle\Exception\PasswordMaximumLengthException;
use Jblab\PasswordValidatorBundle\Exception\PasswordMinimumLengthException;
use Jblab\PasswordValidatorBundle\Exception\PasswordNumberException;
use Jblab\PasswordValidatorBundle\Exception\PasswordSpecialCharacterException;
use Jblab\PasswordValidatorBundle\Exception\PasswordUppercaseException;

/**
 * Class Validator
 * @package Jblab\PasswordValidatorBundle
 */
class PasswordValidator
{
    /**
     * @var int
     */
    protected $minimumLength;
    /**
     * @var int
     */
    protected $maximumLength;
    /**
     * @var bool
     */
    protected $requireSpecialCharacter;
    /**
     * @var bool
     */
    protected $requireUppercase;
    /**
     * @var bool
     */
    protected $requireLowercase;
    /**
     * @var bool
     */
    protected $requireNumber;
    /**
     * @var string
     */
    protected $specialCharacterSet;
    /**
     * @var string|null
     */
    protected $excludedCharacterSet;

    /**
     * PasswordValidator constructor.
     *
     * @param bool        $requireSpecialCharacter
     * @param bool        $requireUppercase
     * @param bool        $requireLowercase
     * @param bool        $requireNumber
     * @param string|null $specialCharacterSet
     * @param int         $minimumLength
     * @param int|null    $maximumLength
     * @param string|null $excludedCharacterSet
     *
     * @throws ConfigurationException
     */
    public function __construct(bool $requireSpecialCharacter, bool $requireUppercase, bool $requireLowercase,
        bool $requireNumber, string $specialCharacterSet = null, int $minimumLength = 1, int $maximumLength = null,
        string $excludedCharacterSet = null
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
     * @param string $password
     * @param bool   $throwError If true the validator will throw an error instead
     *                           of returning false when password isn't valid.
     *
     * @return bool
     * @throws PasswordExcludedCharacterException
     * @throws PasswordLowercaseException
     * @throws PasswordMaximumLengthException
     * @throws PasswordMinimumLengthException
     * @throws PasswordNumberException
     * @throws PasswordSpecialCharacterException
     * @throws PasswordUppercaseException
     */
    public function validate(string $password, bool $throwError = false): bool
    {
        // Length validation
        if (null !== $this->maximumLength) {
            if ($this->maximumLength < strlen($password)) {
                if ($throwError) {
                    throw new PasswordMaximumLengthException(sprintf(
                        'Password must be %d or less characters long.',
                        $this->maximumLength
                    ));
                }

                return false;
            }
        }

        if ($this->minimumLength > strlen($password)) {
            if ($throwError) {
                throw new PasswordMinimumLengthException(sprintf(
                    'Password must be at least %d characters long.',
                    $this->minimumLength
                ));
            }

            return false;
        }

        // Lowercase letter validation
        if ($this->requireLowercase) {
            if (!preg_match('/[a-z]+/', $password)) {
                if ($throwError) {
                    throw new PasswordLowercaseException('Password must contain at least one lowercase letter.');
                }

                return false;
            }
        }

        // Uppercase letter validation
        if ($this->requireUppercase) {
            if (!preg_match('/[A-Z]+/', $password)) {
                if ($throwError) {
                    throw new PasswordUppercaseException('Password must contain at least one uppercase letter.');
                }

                return false;
            }
        }

        // Number validation
        if ($this->requireNumber) {
            if (!preg_match('/[\d]+/', $password)) {
                if ($throwError) {
                    throw new PasswordNumberException('Password must contain at least one number.');
                }

                return false;
            }
        }

        // Special character validation
        if ($this->requireSpecialCharacter) {
            $pattern = sprintf('/[%s]+/', $this->escapeSpecialCharacters($this->specialCharacterSet));
            if (!preg_match($pattern, $password)) {
                if ($throwError) {
                    throw new PasswordSpecialCharacterException(sprintf(
                        'Password must contain at least one special character from this list "%s".',
                        $this->specialCharacterSet
                    ));
                }

                return false;
            }
        }

        // Excluded characters validation
        if ($this->excludedCharacterSet) {
            $pattern = sprintf('/[%s]+/', $this->escapeSpecialCharacters($this->excludedCharacterSet));
            if (preg_match($pattern, $password)) {
                if ($throwError) {
                    throw new PasswordExcludedCharacterException(sprintf(
                        'Password may not contain any of these characters "%s".',
                        $this->excludedCharacterSet
                    ));
                }

                return false;
            }
        }

        return true;
    }

    /**
     * @return string
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
            $regEx .= sprintf('[^*%s]', $this->escapeSpecialCharacters($this->excludedCharacterSet));
        }

        if ($this->minimumLength || $this->maximumLength) {
            $regEx .= sprintf('{%s,%s}', (string)$this->minimumLength, (string)$this->maximumLength);
        }

        $regEx .= '$';

        return $regEx;
    }

    /**
     * @param string $characters
     *
     * @return string
     */
    protected function escapeSpecialCharacters(string $characters): string
    {
        $needEscape = ['[', ']', '(', ')', '{', '}', '*', '+', '?', '|', '^', '$', '.', '\\', '/', '=', '-'];
        $characters = array_unique(str_split($characters));
        $escaped    = [];

        foreach ($characters as $character) {
            $escaped[] = in_array($character, $needEscape) ? '\\' . $character : $character;
        }

        return join('', $escaped);
    }

    /**
     * @throws ConfigurationException
     */
    private function validateConfiguration()
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

        if (strlen($this->specialCharacterSet) === 0 && $this->requireSpecialCharacter) {
            throw new ConfigurationException('Special character is required but character set is empty.');
        }
    }
}