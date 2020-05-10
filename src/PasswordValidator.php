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
     * @throws Exception
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
     * @throws PasswordValidationException
     */
    public function validate(string $password, bool $throwError = false): bool
    {
        // Length validation
        if (null !== $this->maximumLength) {
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

        // Special character validation
        if ($this->requireSpecialCharacter) {
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

        // Excluded characters validation
        $pattern = sprintf('/[%s]+/', $this->escapeSpecialCharacters($this->excludedCharacterSet));
        if (preg_match($pattern, $password)) {
            if ($throwError) {
                throw new PasswordValidationException(sprintf(
                    'Password may not contain any of these characters "%s".',
                    $this->excludedCharacterSet
                ));
            }

            return false;
        }

        return true;
    }

    /**
     * @param string $characters
     *
     * @return string
     */
    protected function escapeSpecialCharacters(string $characters): string
    {
        $needEscape = ['[', ']', '(', ')', '{', '}', '*', '+', '?', '|', '^', '$', '.', '\\', '/'];
        $characters = array_unique(str_split($characters));
        $escaped    = [];

        foreach ($characters as $character) {
            $escaped[] = in_array($character, $needEscape) ? '\\' . $character : $character;
        }

        return join('', $escaped);
    }

    /**
     * @throws Exception
     */
    private function validateConfiguration()
    {
        if ($this->minimumLength < 1) {
            throw new Exception('Invalid minimum length provided, must be at least 1.');
        }

        if (null !== $this->maximumLength && $this->maximumLength < 1) {
            throw new Exception('Password maximum length must be 1 or greater if provided.');
        }

        if (null !== $this->maximumLength && $this->maximumLength < $this->minimumLength) {
            throw new Exception('Maximum password length can\'t be less than minimum password length');
        }

        if (strlen($this->specialCharacterSet) === 0 && $this->requireSpecialCharacter) {
            throw new Exception('Special character is required but character set is empty.');
        }
    }
}