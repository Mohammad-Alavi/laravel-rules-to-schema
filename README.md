# Laravel Rules to Schema

[![Latest Version on Packagist](https://img.shields.io/packagist/v/riley19280/laravel-rules-to-schema.svg?style=flat-square)](https://packagist.org/packages/riley19280/laravel-rules-to-schema)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/riley19280/laravel-rules-to-schema/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/riley19280/laravel-rules-to-schema/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/riley19280/laravel-rules-to-schema/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/riley19280/laravel-rules-to-schema/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/riley19280/laravel-rules-to-schema.svg?style=flat-square)](https://packagist.org/packages/riley19280/laravel-rules-to-schema)

Create a json schema for your Laravel rules 

## Prerequisites

It is recommended to use `FormRequest` classes in your application to use this package. 
This allows for easy extraction of rule objects to pass them into this package.

You can still use this package just by passing in an array of rules, but that is a but more cumbersome.

## Installation

You can install the package via composer:

```bash
composer require riley19280/laravel-rules-to-schema
```

And publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-rules-to-schema-config"
```

## Usage

```php
use \Illuminate\Foundation\Http\FormRequest;

$schema = LaravelRulesToSchema::parse(FormRequest|array $rules): FluentSchema

$schema->compile(); // Returns an array representation of the json schema 
```

## Customization

### JSON Schema Representation

This package relies on [riley19280/fluent-json-schema](https://github.com/riley19280/fluent-json-schema) to build
and represent JSON schemas. For details on how to use and configure the objects returned from the rule parser,
please see the [documentation](https://github.com/riley19280/fluent-json-schema) for that package.


### Registering schemas for custom rules

JSON Schema definitions for custom rules can be registered in the config file by providing the rule name or class name.

The type can be any of the simple JSON schema types (array, boolean, integer, null, number, object, string).
If a more complex type is required, you can provide the class name that implements `LaravelRulesToSchema\Contracts\HasJsonSchema`

```php
// config/rules-to-schema.php

'custom_rule_schemas' => [
    // \CustomPackage\CustomRule::class => \Support\CustomRuleSchemaDefinition::class,
    // \CustomPackage\CustomRule::class => 'string',
    // \CustomPackage\CustomRule::class => ['null', 'string'],
],
```

### Extending the rule parser

Should you want to further customize or tweak how rules are parsed, additional parsers can be added in the config file.
Each parser is run for each rule in the order it is defined in the config file.
Custom parsers must implement the `LaravelRulesToSchema\Contracts\RuleParser` interface.

```php
// config/rules-to-schema.php

'parsers' => [
    ...
    \CustomPackage\CustomParser::class,
],
```

### Tools for package developers

If your package contains a custom rule, you can provide the types as part of the package as well.

In a service provider you can use the following methods to programmatically register a rule or parser:

```php
LaravelRulesToSchema::registerCustomRuleSchema(CustomRule::class, CustomRuleSchemaDefinition::class);
LaravelRulesToSchema::registerParser(CustomParser::class);
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Riley Aven](https://github.com/Riley19280)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
