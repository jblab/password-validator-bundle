<?php

/*
 * This file is part of the Jblab PasswordValidatorBundle package.
 * Copyright (c) 2023-2025 Jblab <https://jblab.io/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Jblab\PasswordValidatorBundle\Tests\IntegrationTests;

use Jblab\PasswordValidatorBundle\JblabPasswordValidatorBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        yield new FrameworkBundle();
        yield new JblabPasswordValidatorBundle();
    }

    public function getCacheDir(): string
    {
        return __DIR__ . '/cache/' . \spl_object_hash($this);
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        if ($this->getEnvironment() === 'test_with_configuration') {
            $loader->load(__DIR__ . '/config/config.yaml');
        }

        $container->loadFromExtension('framework', [
            'test'                  => true,
            'http_method_override'  => false,
            'handle_all_throwables' => true,
            'php_errors'            => ['log' => true],
            'secret'                => 'secret',
        ]);
    }
}
