<?php

namespace Jblab\PasswordValidatorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package Jblab\PasswordValidatorBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('jblab_password_validator');
        $rootNode    = method_exists($treeBuilder, 'getRootNode')
            ? $treeBuilder->getRootNode()
            : $treeBuilder->root('jblab_password_validator');

        $rootNode
            ->children()
            ->integerNode('minimum_length')->defaultValue(8)->info('Minimum password length.')->end()
            ->integerNode('maximum_length')->defaultValue(8)->info('Maximum password length.')->end()
            ->booleanNode('require_special_character')->defaultTrue()->info('Whether or not to require a special character.')->end()
            ->booleanNode('require_uppercase')->defaultTrue()->info('Whether or not to require a uppercase letter.')->end()
            ->booleanNode('require_lowercase')->defaultTrue()->info('Whether or not to require a lowercase letter.')->end()
            ->booleanNode('require_number')->defaultTrue()->info('Whether or not to require a number.')->end()
            ->scalarNode('special_character_set')->defaultValue('!@#$%^&*()_+-=[]{}|\'')->info('String containing all valid special characters')->end()
            ->scalarNode('excluded_character_set')->defaultNull()->info('String containing all invalid characters')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}