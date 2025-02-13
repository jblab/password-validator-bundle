<?php

/*
 * This file is part of the Jblab PasswordValidatorBundle package.
 * Copyright (c) Jblab <https://jblab.io/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jblab\PasswordValidatorBundle\Tests\UnitTests;

use Exception;
use Jblab\PasswordValidatorBundle\Exception\ConfigurationException;
use Jblab\PasswordValidatorBundle\Exception\PasswordExcludedCharacterException;
use Jblab\PasswordValidatorBundle\Exception\PasswordLowercaseException;
use Jblab\PasswordValidatorBundle\Exception\PasswordMaximumLengthException;
use Jblab\PasswordValidatorBundle\Exception\PasswordMinimumLengthException;
use Jblab\PasswordValidatorBundle\Exception\PasswordNumberException;
use Jblab\PasswordValidatorBundle\Exception\PasswordSpecialCharacterException;
use Jblab\PasswordValidatorBundle\Exception\PasswordUppercaseException;
use Jblab\PasswordValidatorBundle\PasswordValidator;
use PHPUnit\Framework\TestCase;

class PasswordValidatorTest extends TestCase
{
    public function testItValidatesMinimumLength(): void
    {
        $validator = new PasswordValidator(false, false, false, false, null, 10, null, null);

        $valid = $validator->validate('1234567890', true);
        $this->assertTrue($valid);

        $valid = $validator->validate('');
        $this->assertFalse($valid);

        $valid = $validator->validate('123456789');
        $this->assertFalse($valid);

        $this->expectException(PasswordMinimumLengthException::class);
        $validator->validate('123456789', true);
    }

    public function testItValidatesMinimumLengthWithRegEx(): void
    {
        $validator = new PasswordValidator(false, false, false, false, null, 10, null, null);
        $regEx     = $validator->getRegEx();

        $matches = preg_match_all('/' . $regEx . '/', '1234567890');
        $this->assertSame($matches, 1);

        $matches = preg_match_all('/' . $regEx . '/', '');
        $this->assertSame($matches, 0);

        $matches = preg_match_all('/' . $regEx . '/', '1');
        $this->assertSame($matches, 0);
    }

    public function testItValidatesMaximumLength(): void
    {
        $validator = new PasswordValidator(false, false, false, false, null, 1, 9, null);

        $valid = $validator->validate('1', true);
        $this->assertTrue($valid);

        $valid = $validator->validate('1234567890');
        $this->assertFalse($valid);

        $this->expectException(PasswordMaximumLengthException::class);
        $validator->validate('1234567890', true);
    }

    public function testItValidatesMaximumLengthWithRegEx(): void
    {
        $validator = new PasswordValidator(false, false, false, false, null, 1, 9, null);
        $regEx     = $validator->getRegEx();

        $matches = preg_match_all('/' . $regEx . '/', '1');
        $this->assertSame($matches, 1);

        $matches = preg_match_all('/' . $regEx . '/', '1234567890');
        $this->assertSame($matches, 0);
    }

    public function testItContainsALowercaseLetter(): void
    {
        $validator = new PasswordValidator(false, false, true, false, null, 1, 10, null);

        $valid = $validator->validate('a', true);
        $this->assertTrue($valid);

        $valid = $validator->validate('A');
        $this->assertFalse($valid);

        $this->expectException(PasswordLowercaseException::class);
        $validator->validate('ABCDEF1234', true);
    }

    public function testItContainsALowercaseLetterWithRegEx(): void
    {
        $validator = new PasswordValidator(false, false, true, false, null, 1, 10, null);
        $regEx     = $validator->getRegEx();

        $matches = preg_match_all('/' . $regEx . '/', 'password');
        $this->assertSame($matches, 1);

        $matches = preg_match_all('/' . $regEx . '/', 'PASSWORD');
        $this->assertSame($matches, 0);
    }

    public function testItContainsAnUppercaseLetter(): void
    {
        $validator = new PasswordValidator(false, true, false, false, null, 1, 10, null);

        $valid = $validator->validate('A', true);
        $this->assertTrue($valid);

        $valid = $validator->validate('a');
        $this->assertFalse($valid);

        $this->expectException(PasswordUppercaseException::class);
        $validator->validate('abcdef1234', true);
    }

    public function testItContainsAnUppercaseLetterWithRegEx(): void
    {
        $validator = new PasswordValidator(false, true, false, false, null, 1, 10, null);
        $regEx     = $validator->getRegEx();

        $matches = preg_match_all('/' . $regEx . '/', 'PASSWORD');
        $this->assertSame($matches, 1);

        $matches = preg_match_all('/' . $regEx . '/', 'password');
        $this->assertSame($matches, 0);
    }

    public function testItContainsANumber(): void
    {
        $validator = new PasswordValidator(false, false, false, true, null, 1, 10, null);

        $valid = $validator->validate('1', true);
        $this->assertTrue($valid);

        $valid = $validator->validate('a');
        $this->assertFalse($valid);

        $this->expectException(PasswordNumberException::class);
        $validator->validate('abcdef', true);
    }

    public function testItContainsANumberWithRegEx(): void
    {
        $validator = new PasswordValidator(false, false, false, true, null, 1, 10, null);
        $regEx     = $validator->getRegEx();

        $matches = preg_match_all('/' . $regEx . '/', 'P4ssw0rd');
        $this->assertSame($matches, 1);

        $matches = preg_match_all('/' . $regEx . '/', 'a');
        $this->assertSame($matches, 0);
    }

    public function testItContainsASpecialCharacter(): void
    {
        $validator = new PasswordValidator(true, false, false, false, '~!@#$%^&*.())+`=-\\', 1, 64, null);

        $valid = $validator->validate('HelloWorld!', true);
        $this->assertTrue($valid);

        $valid = $validator->validate('HelloWorld');
        $this->assertFalse($valid);

        $this->expectException(PasswordSpecialCharacterException::class);
        $validator->validate('HelloWorld_', true);
    }

    public function testItContainsASpecialCharacterWithRegEx(): void
    {
        $validator = new PasswordValidator(true, false, false, false, '~!@#$%^&*.())+`=-\\', 1, 64, null);
        $regEx     = $validator->getRegEx();

        $matches = preg_match_all('/' . $regEx . '/', 'HelloWorld!');
        $this->assertSame($matches, 1);

        $matches = preg_match_all('/' . $regEx . '/', 'HelloWorld');
        $this->assertSame($matches, 0);
    }

    public function testItDoesNotContainsAnExcludedCharacter(): void
    {
        $validator = new PasswordValidator(false, false, false, false, null, 1, 64, '~!@#$%^&*.())+`=-\\');

        $valid = $validator->validate('HelloWorld_', true);
        $this->assertTrue($valid);

        $valid = $validator->validate('HelloWorld^');
        $this->assertFalse($valid);

        $this->expectException(PasswordExcludedCharacterException::class);
        $validator->validate('HelloWorld`', true);
    }

    public function testItDoesNotContainsAnExcludedCharacterWithRegEx(): void
    {
        $validator = new PasswordValidator(false, false, false, false, null, 1, 64, '~!@#$%^&*.())+`=-\\');
        $regEx     = $validator->getRegEx();

        $matches = preg_match_all('/' . $regEx . '/', 'HelloWorld_');
        $this->assertSame($matches, 1);

        $matches = preg_match_all('/' . $regEx . '/', 'HelloWorld^');
        $this->assertSame($matches, 0);
    }

    public function testItDoesNotAcceptInvalidConfiguration(): void
    {
        // Min length > max length
        try {
            new PasswordValidator(false, false, false, false, null, 10, 9, null);
            throw new Exception();
        } catch (Exception $exception) {
            $this->assertInstanceOf(ConfigurationException::class, $exception);
        }

        // Requires a special character without special characters set
        try {
            new PasswordValidator(true, false, false, false, '', 1, 2, null);
            throw new Exception();
        } catch (Exception $exception) {
            $this->assertInstanceOf(ConfigurationException::class, $exception);
        }

        // Min length < 1
        try {
            new PasswordValidator(false, false, false, false, null, 0, 9, null);
            throw new Exception();
        } catch (Exception $exception) {
            $this->assertInstanceOf(ConfigurationException::class, $exception);
        }

        // Max length < 1
        try {
            new PasswordValidator(false, false, false, false, null, 1, 0, null);
            throw new Exception();
        } catch (Exception $exception) {
            $this->assertInstanceOf(ConfigurationException::class, $exception);
        }
    }
}
