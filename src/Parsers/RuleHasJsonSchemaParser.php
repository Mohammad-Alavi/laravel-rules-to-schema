<?php

namespace LaravelRulesToSchema\Parsers;

use FluentJsonSchema\FluentSchema;
use LaravelRulesToSchema\Contracts\HasJsonSchema;
use LaravelRulesToSchema\Contracts\RuleParser;
use PHPUnit\Logging\Exception;

class RuleHasJsonSchemaParser implements RuleParser
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(string $attribute, FluentSchema $schema, array $validationRules, array $nestedRuleset): array|FluentSchema|null
    {
        foreach ($validationRules as $ruleArgs) {
            [$rule, $args] = $ruleArgs;

            $ruleName = is_object($rule) ? get_class($rule) : $rule;

            if ($rule instanceof HasJsonSchema) {
                return $rule->toJsonSchema($attribute);
            } elseif (array_key_exists($ruleName, config('rules-to-schema.custom_rule_schemas'))) {
                $schemaClass = config('rules-to-schema.custom_rule_schemas')[$ruleName];

                $instance = app($schemaClass);

                if (! $instance instanceof HasJsonSchema) {
                    throw new Exception('Custom rule schemas must implement '.HasJsonSchema::class);
                }

                return $instance->toJsonSchema($attribute);
            }
        }

        return $schema;
    }
}
