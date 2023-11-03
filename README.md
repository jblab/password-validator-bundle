# Jblab Password Validator Bundle

[![Build Status](https://travis-ci.org/jblab/password-validator-bundle.svg?branch=master)](https://travis-ci.org/jblab/password-validator-bundle)

This is a way to validate a password based on certain criteria into your Symfony application.

Install the package with:

```console
$ composer require jblab/password-validator-bundle
```

And... that's it. If you're *not* using Symfony Flex, you'll also need to enable the
`Jblab\PasswordValidatorBundle\PasswordValidatorBundle` in your `AppKernel.php` file.

In Symfony 3.4: 

```php
// app/AppKernel.php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            // ... 
            new Jblab\PasswordValidatorBundle\PasswordValidatorBundle(),
        ];
    }
}
```

## Usage
This bundle provides a single service for validating passwords, which
you can autowire by using the `PasswordValidator` type-hint:
```php
// src/Controller/SomeController.php
use Jblab\PasswordValidatorBundle\PasswordValidator;
// ...
class SomeController
{
    public function index(PasswordValidator $passwordValidator)
    {
        $isValid = $passwordValidator->validate('password');
        // ...
    }
}
```
You can also access this service directly using the id
`jblab_password_validator.password_validator`.

## Configuration
The password criteria can be configured directly by
creating a new `config/packages/jblab_password_validator.yaml` file. The
default values are:
```yaml
# config/packages/jblab_password_validator.yaml
jblab_password_validator:

    # Minimum password length.
    minimum_length:       8

    # Maximum password length.
    maximum_length:       64

    # Whether or not to require a special character.
    require_special_character: true

    # Whether or not to require a uppercase letter.
    require_uppercase:    true

    # Whether or not to require a lowercase letter.
    require_lowercase:    true

    # Whether or not to require a number.
    require_number:       true

    # String containing all valid special characters
    special_character_set: '!@#$%^&*()_+-=[]{}|'''

    # String containing all invalid characters
    excluded_character_set: null
```

## Contributing
Of course, open source is fueled by everyone's ability to give just a little bit
of their time for the greater good. If you'd like to see a feature, awesome! You can request it -
but creating a pull request is an even better way to get things done.
Either way, please feel comfortable submitting issues or pull requests: all contributions
and questions are warmly appreciated :).