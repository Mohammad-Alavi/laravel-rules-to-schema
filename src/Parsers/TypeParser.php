<?php

namespace LaravelRulesToSchema\Parsers;

use FluentJsonSchema\FluentSchema;
use Illuminate\Validation\Rules\Enum as EnumRule;
use Illuminate\Validation\Rules\In as InRule;
use LaravelRulesToSchema\Contracts\RuleParser;
use LaravelRulesToSchema\RuleCategory;
use ReflectionClass;

class TypeParser implements RuleParser
{
    public function __invoke(string $property, FluentSchema $schema, array $validationRules, array $nestedRuleset): array|FluentSchema|null
    {
        foreach ($validationRules as $ruleArgs) {
            [$rule, $args] = $ruleArgs;

            $ruleName = is_object($rule) ? get_class($rule) : $rule;

            if (in_array($ruleName, RuleCategory::strings())) {
                $schema->type()->string();
            }
            if (in_array($ruleName, RuleCategory::integers())) {
                $schema->type()->integer();
            }
            if (in_array($ruleName, RuleCategory::numbers())) {
                $schema->type()->number();
            }
            if (in_array($ruleName, RuleCategory::booleans())) {
                $schema->type()->boolean();
            }
            if (in_array($ruleName, RuleCategory::arrays())) {
                // Check if what we are dealing with is not an object type with properties
                if (count(array_diff_key($nestedRuleset, array_flip([config('rules-to-schema.validation_rule_token')]))) == 0) {
                    $schema->type()->array();
                }
            }
            if (in_array($ruleName, RuleCategory::nullables())) {
                $schema->type()->null();
            }

            if ($rule instanceof EnumRule) {
                $this->parseEnumRule($schema, $rule);
            }

            if ($rule instanceof InRule || $rule === 'in') {
                $this->parseInRule($schema, $rule, $args);
            }
        }

        return $schema;
    }

    protected function parseInRule(FluentSchema $schema, mixed $ruleName, ?array $args): void
    {
        if (is_string($ruleName)) {
            $values = array_map(function (mixed $value) {
                if (is_numeric($value)) {
                    if (ctype_digit($value)) {
                        return intval($value);
                    }

                    return floatval($value);
                }

                return $value;
            }, $args);
        } else {
            $values = invade($ruleName)->values;
        }

        $isString = true;
        $isInt = true;
        $isNumeric = true;

        foreach ($values as $value) {
            if (is_string($value)) {
                $isString = $isString && true;
                $isInt = false;
                $isNumeric = false;
            }

            if (is_int($value)) {

                $isString = false;
                $isInt = $isInt && true;
                $isNumeric = false;
            }

            if (is_float($value)) {
                $isString = false;
                $isInt = false;
                $isNumeric = $isNumeric && true;
            }
        }

        if ($isString) {
            $schema->type()->string();
        }
        if ($isInt) {
            $schema->type()->integer();
        }
        if ($isNumeric) {
            $schema->type()->number();
        }
    }

    protected function parseEnumRule(FluentSchema $schema, EnumRule $rule): void
    {
        $enumType = invade($rule)->type;

        $reflection = new ReflectionClass($enumType);

        if (
            $reflection->implementsInterface(\BackedEnum::class)
            && count($reflection->getConstants()) > 0
        ) {
            $value = array_values($reflection->getConstants())[0]->value;

            if (is_string($value)) {
                $schema->type()->string();
            }
            if (is_int($value)) {
                $schema->type()->integer();
            }
        }
    }
}
