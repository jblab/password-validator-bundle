# Password Validator Bundle <img src="https://assets.jblab.info/2024/03/17/jblab-logo-with-text.26da23672fc44c17078dc8ce2ff8495ddb190163.webp" alt="jblab logo" width="120" align="right" style="max-width: 100%">

**Password Validator Bundle** is a Symfony bundle designed to validate passwords effectively based on highly
customizable criteria. With this bundle, you can ensure that your application's passwords meet robust security standards
effortlessly.

---

## Table of Contents

<!-- TOC -->
* [Features](#features)
* [Installation](#installation)
  * [Applications that use Symfony Flex](#applications-that-use-symfony-flex)
  * [Applications that don't use Symfony Flex](#applications-that-dont-use-symfony-flex)
    * [Step 1: Download the Bundle](#step-1-download-the-bundle)
    * [Step 2: Enable the Bundle](#step-2-enable-the-bundle)
* [Usage](#usage)
  * [Using the Service](#using-the-service)
  * [Create an Object Manually](#create-an-object-manually)
  * [Using a Regular Expression](#using-a-regular-expression)
* [Configuration](#configuration)
  * [Default Configurations](#default-configurations)
  * [Customizing the Validation Logic](#customizing-the-validation-logic)
* [Examples](#examples)
  * [Example 1: Enforcing Strict Rules](#example-1-enforcing-strict-rules)
  * [Example 2: Restrictive Rules with Excluded Characters](#example-2-restrictive-rules-with-excluded-characters)
<!-- TOC -->

---

## Features

- Quick installation and seamless integration with Symfony projects.
- Comprehensive password validation tailored to your security requirements.
- Easily configurable via `YAML` for flexibility in defining rules.
- Supports modern password security best practices, including checks for:
    - Minimum and maximum password length.
    - Inclusion of uppercase, lowercase, numeric, and special characters.
    - Restricted character sets.
- Fully autowireable service with an intuitive API.

---

## Installation

### Applications that use Symfony Flex

Open a command console, enter your project directory, and execute:

```bash
composer require jblab/password-validator-bundle
```

### Applications that don't use Symfony Flex

#### Step 1: Download the Bundle

Execute the following to download the latest stable version of the bundle:

```bash
composer require jblab/password-validator-bundle
```

#### Step 2: Enable the Bundle

Add it to your `config/bundles.php` file to register the bundle:

```php
// config/bundles.php

return [
    // ...
    Jblab\PasswordValidatorBundle\JblabPasswordValidatorBundle::class => ['all' => true],
];
```

---

## Usage

### Using the Service

The Password Validator Bundle provides an elegantly simple service for validating passwords. You can inject the
`PasswordValidatorInterface` service into your code and use it as follows:

```php
use Jblab\PasswordValidatorBundle\PasswordValidatorInterface;

class SomeController
{
    public function __construct(private readonly PasswordValidatorInterface $passwordValidator)
    {
    }

    public function index()
    {
        $password = 'StrongPass1!';
        $isValid = $this->passwordValidator->validate($password);

        if (!$isValid) {
            // Handle invalid password, e.g., display an error message
        }
        
        // Handle the valid password case
    }
}
```

The service can also be accessed directly using its service ID: `jblab_password_validator.password_validator`.

### Create an Object Manually

You can also create a password validator object manually and configure its validation logic according to your specific
requirements. For instance:

```php
use Jblab\PasswordValidatorBundle\PasswordValidator;

// ...

$validator = new PasswordValidator(
    requireSpecialCharacter: true,
    requireUppercase: true,
    requireLowercase: true,
    requireNumber: true,
    specialCharacterSet: '-_!',
    minimumLength: 8,
    maximumLength: 24,
    excludedCharacterSet: '@*'
);

$valid = $validator->validate('Password!1');
```

This example demonstrates how to:

- Allow special characters limited to `-`, `_`, and `!`.
- Enforce a password length between 8 and 24 characters.
- Exclude specific characters like `@` and `*` from being used in passwords.
- Require the presence of at least one uppercase letter, one lowercase letter, one number, and one special character in
  the password.

By using this approach, you have full control over the validator's configuration without needing to rely on the default
settings.

### Using a Regular Expression

The password validator also provides a method to generate a regular expression that can be used for password validation.

```php
/** @var Jblab\PasswordValidatorBundle\PasswordValidator $passwordValidator */
$regEx = $passwordValidator->getRegEx();
$match = preg_match('/' . $regEx . '/', 'Password!1') === 1;
```

In this example:

- The `getRegEx()` method generates a regular expression based on the validator's configuration.
- The `preg_match` function checks if the specified password matches the generated pattern.
- `$isValid` is set to `true` if the password matches the validation rules, or `false` otherwise.

This approach allows you to leverage the validator's logic directly through regular expressions when necessary.

---

## Configuration

The bundle allows you to customize password validation rules by configuring the following options in your
`config/packages/jblab_password_validator.yaml` file.

### Default Configurations

Below are the default configuration values:

```yaml
# config/packages/jblab_password_validator.yaml
jblab_password_validator:

  # Minimum password length.
  minimum_length: 8

  # Maximum password length.
  maximum_length: 64

  # Require at least one special character.
  require_special_character: true

  # Require at least one uppercase letter.
  require_uppercase: true

  # Require at least one lowercase letter.
  require_lowercase: true

  # Require at least one number.
  require_number: true

  # Allowed special characters.
  special_character_set: '!@#$%^&*()_+-=[]{}|'''

  # Characters to exclude from passwords.
  excluded_character_set: null
```

---

### Customizing the Validation Logic

- **Password Length**:
    - Set both `minimum_length` and `maximum_length` values to define password length constraints.
- **Character Requirements**:
    - Toggle `require_special_character`, `require_uppercase`, `require_lowercase`, and `require_number` to define which
      character types are mandatory.
- **Restricting Characters**:
    - Use `special_character_set` to specify acceptable symbols.
    - Define `excluded_character_set` to block specific characters.

---

## Examples

Here are some additional examples to demonstrate the flexibility of the bundle configuration:

### Example 1: Enforcing Strict Rules

```yaml
# config/packages/jblab_password_validator.yaml
jblab_password_validator:
  minimum_length: 12
  maximum_length: 32
  require_special_character: true
  require_uppercase: true
  require_lowercase: true
  require_number: true
```

This configuration enforces a password policy that mandates:

- A password length between 12 and 32 characters.
- The inclusion of at least one special character, uppercase letter, lowercase letter, and number.

### Example 2: Restrictive Rules with Excluded Characters

```yaml
# config/packages/jblab_password_validator.yaml
jblab_password_validator:
  special_character_set: '!@#$%*+='
  excluded_character_set: ';/|-'
  require_uppercase: false
```

This configuration:

- Permits only specific special characters.
- Excludes problematic characters such as `;`, `/`, `|`, and `-` from passwords.
- Removes the requirement for uppercase letters.
