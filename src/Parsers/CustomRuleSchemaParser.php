<?php

namespace LaravelRulesToSchema\Parsers;

use FluentJsonSchema\Enums\JsonSchemaType;
use FluentJsonSchema\FluentSchema;
use LaravelRulesToSchema\Contracts\HasJsonSchema;
use LaravelRulesToSchema\Contracts\RuleParser;
use LaravelRulesToSchema\Facades\LaravelRulesToSchema;
use PHPUnit\Logging\Exception;

class CustomRuleSchemaParser implements RuleParser
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
            } elseif (array_key_exists($ruleName, LaravelRulesToSchema::getCustomRuleSchemas())) {
                $typehint = LaravelRulesToSchema::getCustomRuleSchemas()[$ruleName];

                if (is_string($typehint)) {
                    if (class_exists($typehint)) {
                        $instance = app($typehint);

                        if (! $instance instanceof HasJsonSchema) {
                            throw new Exception('Custom rule schemas must implement '.HasJsonSchema::class);
                        }

                        return $instance->toJsonSchema($attribute);
                    } else {
                        $schema->type()->fromString($typehint);
                    }
                } elseif ($typehint instanceof JsonSchemaType) {
                    $schema->type()->fromString($typehint->value);
                } elseif (is_array($typehint)) {
                    foreach ($typehint as $type) {
                        if ($type instanceof JsonSchemaType) {
                            $schema->type()->fromString($type->value);
                        } else {
                            $schema->type()->fromString($type);
                        }
                    }
                }
            }
        }

        return $schema;
    }
}
