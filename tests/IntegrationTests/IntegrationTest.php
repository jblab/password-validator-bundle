<?php

/**
 * @noinspection PhpUnhandledExceptionInspection
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MultipleClasses
 */

/*
 * This file is part of the Jblab PasswordValidatorBundle package.
 * Copyright (c) Jblab <https://jblab.io/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jblab\PasswordValidatorBundle\Tests\IntegrationTests;

use Exception;
use Jblab\PasswordValidatorBundle\JblabPasswordValidatorBundle;
use Jblab\PasswordValidatorBundle\PasswordValidator;
use Jblab\PasswordValidatorBundle\PasswordValidatorInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

final class IntegrationTest extends TestCase
{
    public function testServiceWiring(): void
    {
        $kernel = new JblabPasswordValidatorTestingKernel();
        $kernel->boot();
        $container = $kernel->getContainer();

        $validator = $container->get('jblab_password_validator.password_validator');
        $this->assertInstanceOf(PasswordValidator::class, $validator);
        $this->assertIsBool($validator->validate('password'));
    }

    public function testServiceWiringWithConfiguration(): void
    {
        $kernel = new JblabPasswordValidatorTestingKernel([
            'minimum_length'            => 1,
            'maximum_length'            => 5,
            'require_special_character' => false,
            'require_uppercase'         => false,
            'require_lowercase'         => false,
            'require_number'            => false,
            'special_character_set'     => null,
            'excluded_character_set'    => null
        ]);
        $kernel->boot();
        $container = $kernel->getContainer();

        /** @var PasswordValidatorInterface $validator */
        $validator = $container->get('jblab_password_validator.password_validator');
        $this->assertTrue($validator->validate('1234'));
    }
}

final class JblabPasswordValidatorTestingKernel extends Kernel
{
    private array $passwordValidatorConfig;

    public function __construct(array $passwordValidatorConfig = [])
    {
        parent::__construct('test', true);
        $this->passwordValidatorConfig = $passwordValidatorConfig;
    }

    /**
     * @return JblabPasswordValidatorBundle[]
     */
    public function registerBundles(): array
    {
        return [
            new JblabPasswordValidatorBundle(),
        ];
    }

    /**
     * @throws Exception
     */
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(function (ContainerBuilder $container) {
            $container->loadFromExtension('jblab_password_validator', $this->passwordValidatorConfig);
        });
    }

    public function getCacheDir(): string
    {
        return __DIR__ . '/cache/' . spl_object_hash($this);
    }
}
