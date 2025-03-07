<?php

/*
 * This file is part of the Jblab PasswordValidatorBundle package.
 * Copyright (c) 2023-2025 Jblab <https://jblab.io/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jblab\PasswordValidatorBundle\Tests\IntegrationTests;

use Jblab\PasswordValidatorBundle\PasswordValidator;
use Jblab\PasswordValidatorBundle\PasswordValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class WiringTest extends KernelTestCase
{
    public function testServiceWiring(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $validator = $container->get(PasswordValidatorInterface::class);
        $this->assertInstanceOf(PasswordValidator::class, $validator);

        $validator = $container->get('jblab_password_validator.password_validator');
        $this->assertInstanceOf(PasswordValidator::class, $validator);
        $this->assertIsBool($validator->validate('password'));
    }

    public function testServiceWiringWithConfiguration(): void
    {
        self::bootKernel(['environment' => 'test_with_configuration']);

        $container = self::getContainer();

        /** @var PasswordValidatorInterface $validator */
        $validator = $container->get('jblab_password_validator.password_validator');
        $this->assertTrue($validator->validate('1234'));
    }
}
