<?php

namespace Jblab\PasswordValidatorBundle;

use Exception;
use Jblab\PasswordValidatorBundle\Exception\PasswordValidationException;

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

    public function __construct(bool $requireSpecialCharacter, bool $requireUppercase, bool $requireLowercase,
        bool $requireNumber, string $specialCharacterSet, int $minimumLength = 1, int $maximumLength = null
    ) {
        $this->minimumLength           = $minimumLength;
        $this->maximumLength           = $maximumLength;
        $this->requireSpecialCharacter = $requireSpecialCharacter;
        $this->requireUppercase        = $requireUppercase;
        $this->requireLowercase        = $requireLowercase;
        $this->requireNumber           = $requireNumber;
        $this->specialCharacterSet     = $specialCharacterSet;
    }

    /**
     * @param string $password
     * @param bool   $throwError If true the validator will throw an error instead
     *                           of returning false when password isn't valid.
     *
     * @return bool
     * @throws Exception
     * @throws PasswordValidationException
     */
    public function validate(string $password, bool $throwError = false): bool
    {
        // Length validation
        if (null !== $this->maximumLength) {
            if ($this->maximumLength < 1) {
                throw new Exception('Password maximum length must be 1 or greater if provided.');
            }
            if ($this->maximumLength < strlen($password)) {
                if ($throwError) {
                    throw new PasswordValidationException(sprintf(
                        'Password must be %d or less characters long.',
                        $this->maximumLength
                    ));
                }

                return false;
            }
        }

        if ($this->minimumLength > strlen($password)) {
            if ($throwError) {
                throw new PasswordValidationException(sprintf(
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
                    throw new PasswordValidationException('Password must contain at least one lowercase letter.');
                }

                return false;
            }
        }

        // Lowercase letter validation
        if ($this->requireUppercase) {
            if (!preg_match('/[A-Z]+/', $password)) {
                if ($throwError) {
                    throw new PasswordValidationException('Password must contain at least one uppercase letter.');
                }

                return false;
            }
        }

        // Number validation
        if ($this->requireNumber) {
            if (!preg_match('/[\d]+/', $password)) {
                if ($throwError) {
                    throw new PasswordValidationException('Password must contain at least one number.');
                }

                return false;
            }
        }

        // Special character
        if ($this->requireSpecialCharacter) {
            if (strlen($this->specialCharacterSet) === 0) {
                throw new Exception('Special character is required but character set is empty.');
            }

            $pattern = sprintf('/[%s]+/', $this->escapeSpecialCharacters($this->specialCharacterSet));
            if (!preg_match($pattern, $password)) {
                if ($throwError) {
                    throw new PasswordValidationException(sprintf(
                        'Password must contain at least one special character from this list "%s".',
                        $this->specialCharacterSet
                    ));
                }

                return false;
            }
        }

        return true;
    }

    /**
     * @param string $characters
     *
     * @return array
     */
    protected function escapeSpecialCharacters(string $characters): array
    {
        $needEscape = ['[', ']', '(', ')', '{', '}', '*', '+', '?', '|', '^', '$', '.', '\\'];
        $characters = array_unique(explode('', $characters));
        $escaped    = [];

        foreach ($characters as $character) {
            $escaped[] = in_array($character, $needEscape) ? '\\' : '' . $character;
        }

        return $escaped;
    }
}