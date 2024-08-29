<?php

use LaravelRulesToSchema\LaravelRuleType;
use LaravelRulesToSchema\Parsers\ConfirmedParser;
use LaravelRulesToSchema\Parsers\EnumParser;
use LaravelRulesToSchema\Parsers\ExcludedParser;
use LaravelRulesToSchema\Parsers\FormatParser;
use LaravelRulesToSchema\Parsers\MiscPropertyParser;
use LaravelRulesToSchema\Parsers\NestedObjectParser;
use LaravelRulesToSchema\Parsers\RequiredParser;
use LaravelRulesToSchema\Parsers\RuleHasJsonSchemaParser;
use LaravelRulesToSchema\Parsers\TypeParser;

return [

    /*
     * The key to store validation rules under
     * This should be unique and not match any real property names
     * that will be submitted in requests.
     */
    'validation_rule_token' => '##_VALIDATION_RULES_##',

    /*
     * The parsers to run rules through
     */
    'parsers' => [
        TypeParser::class,
        NestedObjectParser::class,
        RequiredParser::class,
        MiscPropertyParser::class,
        FormatParser::class,
        EnumParser::class,
        ExcludedParser::class,
        ConfirmedParser::class,
        RuleHasJsonSchemaParser::class,
    ],

    /*
     * For convenience, simple types for custom rules can be added here
     */
    'rule_type_map' => [
        'string' => [
            ...LaravelRuleType::string(),
        ],
        'integer' => [
            ...LaravelRuleType::integer(),
        ],
        'number' => [
            ...LaravelRuleType::number(),
        ],
        'boolean' => [
            ...LaravelRuleType::boolean(),
        ],
        'nullable' => [
            ...LaravelRuleType::nullable(),
        ],
        'array' => [
            ...LaravelRuleType::array(),
        ],
        'exclude' => [
            ...LaravelRuleType::exclude(),
        ],
    ],

    /*
     * Third party rules that you can provide custom schema definitions for
     */
    'custom_rule_schemas' => [
        // \CustomPackage\CustomRule::class => \Support\CustomRuleSchemaDefinition::class,
    ],
];
