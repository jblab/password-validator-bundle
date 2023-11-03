<?php

namespace Jblab\PasswordValidatorBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class JblabPasswordValidatorExtension extends Extension
{
    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container)
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