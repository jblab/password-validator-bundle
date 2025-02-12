<?php

/*
 * This file is part of the Jblab PasswordValidatorBundle package.
 * Copyright (c) Jblab <https://jblab.io/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jblab\PasswordValidatorBundle;

use Exception;
use Jblab\PasswordValidatorBundle\Exception\PasswordExcludedCharacterException;
use Jblab\PasswordValidatorBundle\Exception\PasswordLowercaseException;
use Jblab\PasswordValidatorBundle\Exception\PasswordMaximumLengthException;
use Jblab\PasswordValidatorBundle\Exception\PasswordMinimumLengthException;
use Jblab\PasswordValidatorBundle\Exception\PasswordNumberException;
use Jblab\PasswordValidatorBundle\Exception\PasswordSpecialCharacterException;
use Jblab\PasswordValidatorBundle\Exception\PasswordUppercaseException;
use Jblab\PasswordValidatorBundle\Exception\PasswordValidationException;

interface PasswordValidatorInterface
{
    /**
     * @throws PasswordExcludedCharacterException
     * @throws PasswordLowercaseException
     * @throws PasswordMaximumLengthException
     * @throws PasswordMinimumLengthException
     * @throws PasswordNumberException
     * @throws PasswordSpecialCharacterException
     * @throws PasswordUppercaseException
     * @throws PasswordValidationException
     * @throws Exception
     */
    public function validate(string $password, bool $throwException = false): bool;
}
