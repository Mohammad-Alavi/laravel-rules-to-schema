<?php

use Illuminate\Support\Facades\Config;
use LaravelRulesToSchema\Facades\LaravelRulesToSchema;
use LaravelRulesToSchema\Tests\Fixtures\CustomRule;
use LaravelRulesToSchema\Tests\Fixtures\CustomRuleSchemaDefinition;

test('custom rule types', function () {
    Config::set('rules-to-schema.rule_type_map.string', ['custom_string_rule', CustomRule::class]);
    Config::set('rules-to-schema.rule_type_map.integer', ['custom_integer_rule']);
    Config::set('rules-to-schema.rule_type_map.number', ['custom_number_rule']);
    Config::set('rules-to-schema.rule_type_map.boolean', ['custom_boolean_rule']);
    Config::set('rules-to-schema.rule_type_map.array', ['custom_array_rule']);
    Config::set('rules-to-schema.rule_type_map.nullable', ['custom_nullable_rule']);
    Config::set('rules-to-schema.rule_type_map.exclude', ['custom_exclude_rule']);

    $rules = [
        'string'       => ['custom_string_rule'],
        'string_class' => [new CustomRule],
        'integer'      => ['custom_integer_rule'],
        'number'       => ['custom_number_rule'],
        'boolean'      => ['custom_boolean_rule'],
        'array'        => ['custom_array_rule'],
        'nullable'     => ['custom_nullable_rule'],
        'exclude'      => ['custom_exclude_rule'],
    ];

    expect(LaravelRulesToSchema::parse($rules)->compile())
        ->toMatchSnapshot();
});

test('custom rule can have custom schema', function () {
    Config::set('rules-to-schema.custom_rule_schemas', [
        CustomRule::class        => CustomRuleSchemaDefinition::class,
        'custom_registered_rule' => CustomRuleSchemaDefinition::class,
    ]);

    $rules = [
        'custom_with_schema'     => [new CustomRule],
        'custom_registered_rule' => ['custom_registered_rule'],
    ];

    expect(LaravelRulesToSchema::parse($rules)->compile())
        ->toMatchSnapshot();
});
