<?php

/*
 * This file is part of the Jblab PasswordValidatorBundle package.
 * Copyright (c) Jblab <https://jblab.io/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jblab\PasswordValidatorBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Extension\Extension;

class JblabPasswordValidatorExtension extends Extension
{
    /**
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $definition = $container->getDefinition('jblab_password_validator.password_validator');
        $definition->setArgument(0, $config['require_special_character']);
        $definition->setArgument(1, $config['require_uppercase']);
        $definition->setArgument(2, $config['require_lowercase']);
        $definition->setArgument(3, $config['require_number']);
        $definition->setArgument(4, $config['special_character_set']);
        $definition->setArgument(5, $config['minimum_length']);
        $definition->setArgument(6, $config['maximum_length']);
        $definition->setArgument(7, $config['excluded_character_set']);
    }
}
