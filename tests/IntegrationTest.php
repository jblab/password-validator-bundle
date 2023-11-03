<?php

namespace Jblab\PasswordValidatorBundle\Tests;

use Exception;
use Jblab\PasswordValidatorBundle\JblabPasswordValidatorBundle;
use Jblab\PasswordValidatorBundle\PasswordValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
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
        $kernel = new JblabPasswordValidatorTestingKernel();
        $kernel->boot();
        $container = $kernel->getContainer();

        $validator = $container->get('jblab_password_validator.password_validator');
        $this->assertInstanceOf(PasswordValidator::class, $validator);
        $this->assertIsBool($validator->validate('password'));
    }

    public function testServiceWiringWithConfiguration()
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

        $validator = $container->get('jblab_password_validator.password_validator');
        $this->assertTrue($validator->validate('1234'));
    }
}

/**
 * Class JblabPasswordValidatorTestingKernel
 * @package Jblab\PasswordValidatorBundle\Tests
 */
class JblabPasswordValidatorTestingKernel extends Kernel
{
    /**
     * @var array
     */
    private $passwordValidatorConfig;

    /**
     * JblabPasswordValidatorTestingKernel constructor.
     *
     * @param array $passwordValidatorConfig
     */
    public function __construct(array $passwordValidatorConfig = [])
    {
        parent::__construct('test', true);
        $this->passwordValidatorConfig = $passwordValidatorConfig;
    }

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
     *
     * @throws Exception
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(function (ContainerBuilder $container) {
            $container->loadFromExtension('jblab_password_validator', $this->passwordValidatorConfig);
        });
    }

    /**
     * @return string
     */
    public function getCacheDir()
    {
        return __DIR__ . '/cache/' . spl_object_hash($this);
    }
}