<?php

/*
 * This file is part of the Jblab PasswordValidatorBundle package.
 * Copyright (c) 2023-2025 Jblab <https://jblab.io/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Jblab\PasswordValidatorBundle\PasswordValidator;
use Jblab\PasswordValidatorBundle\PasswordValidatorInterface;

return function (ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services
        ->set('jblab_password_validator.password_validator', PasswordValidator::class)
        ->public()
    ;

    $services
        ->alias(PasswordValidatorInterface::class, 'jblab_password_validator.password_validator')
    ;

    $services
        ->alias(PasswordValidator::class, 'jblab_password_validator.password_validator')
        ->deprecate(
            'jblab/password-validator-bundle',
            '2.0.2',
            'The "%alias_id%" service has been deprecated and will be removed in version 3.'
            . ' Use "Jblab\PasswordValidatorBundle\PasswordValidatorInterface" instead.'
        )
    ;
};
