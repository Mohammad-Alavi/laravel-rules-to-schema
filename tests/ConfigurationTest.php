<?php

use FluentJsonSchema\Enums\JsonSchemaType;
use Illuminate\Support\Facades\Config;
use LaravelRulesToSchema\Facades\LaravelRulesToSchema;
use LaravelRulesToSchema\Tests\Fixtures\CustomRule;
use LaravelRulesToSchema\Tests\Fixtures\CustomRuleSchemaDefinition;
use LaravelRulesToSchema\Tests\Fixtures\TestCustomParser;

test('custom rule can have custom schema', function () {
    Config::set('rules-to-schema.custom_rule_schemas', [
        CustomRule::class        => CustomRuleSchemaDefinition::class,
        'custom_registered_rule' => CustomRuleSchemaDefinition::class,

        'custom_string_rule'   => 'string',
        'custom_integer_rule'  => 'integer',
        'custom_number_rule'   => 'number',
        'custom_boolean_rule'  => 'boolean',
        'custom_array_rule'    => 'array',
        'custom_nullable_rule' => 'null',

        'custom_rule_as_enum'       => JsonSchemaType::STRING,
        'custom_rule_as_enum_array' => [JsonSchemaType::NULL, JsonSchemaType::NUMBER],

        'custom_multiple_types' => ['null', 'string'],
    ]);

    $rules = [
        'custom_with_schema'     => [new CustomRule],
        'custom_registered_rule' => ['custom_registered_rule'],

        'integer'  => ['custom_integer_rule'],
        'number'   => ['custom_number_rule'],
        'boolean'  => ['custom_boolean_rule'],
        'array'    => ['custom_array_rule'],
        'nullable' => ['custom_nullable_rule'],

        'custom_rule_as_enum'       => ['custom_rule_as_enum'],
        'custom_rule_as_enum_array' => ['custom_rule_as_enum_array'],

        'custom_multiple_types' => ['custom_multiple_types'],
    ];

    expect(LaravelRulesToSchema::parse($rules)->compile())
        ->toMatchSnapshot();
});

test('can register custom parsers', function () {
    LaravelRulesToSchema::registerParser(TestCustomParser::class);
    $rules = [
        'custom' => ['test_custom_parser'],
    ];

    expect(LaravelRulesToSchema::parse($rules)->compile())
        ->toMatchSnapshot();
});

test('can register custom rule schemas', function () {
    LaravelRulesToSchema::registerCustomRuleSchema('custom', ['null', 'string']);
    $rules = [
        'custom' => ['custom'],
    ];

    expect(LaravelRulesToSchema::parse($rules)->compile())
        ->toMatchSnapshot();
});
