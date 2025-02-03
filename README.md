# Password Validator Bundle <img src="https://assets.jblab.info/2024/03/17/jblab-logo-with-text.26da23672fc44c17078dc8ce2ff8495ddb190163.webp" alt="jblab logo" width="120" align="right" style="max-width: 100%">

[![License](https://img.shields.io/badge/License-Apache%202.0-blue.svg?style=flat-square)](LICENSE) [![Latest Release](https://img.shields.io/github/release/jblab/password-validator-bundle.svg?style=flat-square)](https://github.com/jblab/password-validator-bundle/releases/latest) ![GitHub Actions Workflow Status](https://img.shields.io/github/actions/workflow/status/jblab/password-validator-bundle/ci.yaml?style=flat-square)

**Password Validator Bundle** is a Symfony package to help you validate passwords against customizable criteria with
ease.

## Installation

Install the bundle via Composer:

```shell
composer require jblab/password-validator-bundle
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
        $password = 'StrongPass1!';
        $isValid = $passwordValidator->validate($password);

        if (!$isValid) {
            // Handle invalid password, e.g., return error response
        }
        // Continue with your logic for valid passwords
    }
}
```

You can also access this service directly using the id
`jblab_password_validator.password_validator`.

## Configuration

To customize password validation criteria, add the configuration file `config/packages/jblab_password_validator.yaml`
with the following options (default values shown):

```yaml
# config/packages/jblab_password_validator.yaml
jblab_password_validator:

  # Minimum password length.
  minimum_length: 8

  # Maximum password length.
  maximum_length: 64

  # Whether or not to require a special character.
  require_special_character: true

  # Whether or not to require a uppercase letter.
  require_uppercase: true

  # Whether or not to require a lowercase letter.
  require_lowercase: true

  # Whether or not to require a number.
  require_number: true

  # String containing all valid special characters
  special_character_set: '!@#$%^&*()_+-=[]{}|'''

  # String containing all invalid characters
  excluded_character_set: null
```

## Contributing

We love contributions! If you have an idea for a feature, feel free to open an issue or, better yet, submit a pull request. We welcome participation, whether you're submitting code, reporting bugs, or asking questions.
Check out the [contribution guidelines](CONTRIBUTING.md) to get started.

## License

This bundle is released under the [Apache 2.0 License](LICENSE). Feel free to use it in your projects.
