<?php

return [

    /*
     * The key to store validation rules under
     * This should be unique and not match any real property names
     * that will be submitted in requests.
     */
    'validation_rule_token' => '##_VALIDATION_RULES_##',

    /*
     * The pipeline to run rules through
     */
    'pipes' => [
        \LaravelRulesToSchema\Parsers\TypeParser::class,
        \LaravelRulesToSchema\Parsers\NestedObjectParser::class,
        \LaravelRulesToSchema\Parsers\RequiredParser::class,
        \LaravelRulesToSchema\Parsers\MiscPropertyParser::class,
        \LaravelRulesToSchema\Parsers\FormatParser::class,
        \LaravelRulesToSchema\Parsers\EnumParser::class,
        \LaravelRulesToSchema\Parsers\ExcludedParser::class,
        \LaravelRulesToSchema\Parsers\ConfirmedParser::class,
    ],

    'rule_type_map' => [
        'string' => [],
        'integer' => [],
        'number' => [],
        'null' => [],
    ],

];
