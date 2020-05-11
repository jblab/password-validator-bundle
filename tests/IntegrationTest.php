<?php

namespace Jblab\PasswordValidatorBundle\Tests;

use Jblab\PasswordValidatorBundle\JblabPasswordValidatorBundle;
use Jblab\PasswordValidatorBundle\PasswordValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Class IntegrationTest
 * @package Jblab\PasswordValidatorBundle\Tests
 */
class IntegrationTest extends TestCase
{
    public function testServiceWiring()
    {
        $kernel = new JblabPasswordValidatorTestingKernel('test', true);
        $kernel->boot();
        $container = $kernel->getContainer();

        $validator = $container->get('jblab_password_validator.password_validator');
        $this->assertInstanceOf(PasswordValidator::class, $validator);
        $this->assertIsBool($validator->validate('password'));
    }
}

/**
 * Class JblabPasswordValidatorTestingKernel
 * @package Jblab\PasswordValidatorBundle\Tests
 */
class JblabPasswordValidatorTestingKernel extends Kernel
{

    /**
     * @return BundleInterface[]
     */
    public function registerBundles()
    {
        return [
            new JblabPasswordValidatorBundle()
        ];
    }

    /**
     * @param LoaderInterface $loader
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
    }
}