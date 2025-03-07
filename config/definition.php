<?php

/*
 * This file is part of the Jblab PasswordValidatorBundle package.
 * Copyright (c) 2023-2025 Jblab <https://jblab.io/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;

return static function (DefinitionConfigurator $definition): void {
    // @formatter:off
    $definition->rootNode()
        ->children()
            ->integerNode('minimum_length')
                ->defaultValue(8)
                ->info('Minimum password length.')
            ->end()
            ->integerNode('maximum_length')
                ->defaultValue(64)
                ->info('Maximum password length.')
            ->end()
            ->booleanNode('require_special_character')
                ->defaultTrue()
                ->info('Whether or not to require a special character.')
            ->end()
            ->booleanNode('require_uppercase')
                ->defaultTrue()
                ->info('Whether or not to require a uppercase letter.')
            ->end()
            ->booleanNode('require_lowercase')
                ->defaultTrue()
                ->info('Whether or not to require a lowercase letter.')
            ->end()
            ->booleanNode('require_number')
                ->defaultTrue()
                ->info('Whether or not to require a number.')
            ->end()
            ->scalarNode('special_character_set')
                ->defaultValue('!@#$%^&*()_+-=[]{}|\'')
                ->info('String containing all valid special characters')
            ->end()
            ->scalarNode('excluded_character_set')
                ->defaultNull()
                ->info('String containing all invalid characters')
            ->end()
        ->end()
    ;
    // @formatter:on
};
