<?php

/*
 * This file is part of the Jblab PasswordValidatorBundle package.
 * Copyright (c) 2023-2025 Jblab <https://jblab.io/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jblab\PasswordValidatorBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class JblabPasswordValidatorBundle extends AbstractBundle
{
    /** @phpstan-ignore missingType.iterableValue */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.php');
        $container->services()
            ->get('jblab_password_validator.password_validator')
            ->arg(0, $config['require_special_character'])
            ->arg(1, $config['require_uppercase'])
            ->arg(2, $config['require_lowercase'])
            ->arg(3, $config['require_number'])
            ->arg(4, $config['special_character_set'])
            ->arg(5, $config['minimum_length'])
            ->arg(6, $config['maximum_length'])
            ->arg(7, $config['excluded_character_set'])
        ;
    }

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->import('../config/definition.php');
    }
}
