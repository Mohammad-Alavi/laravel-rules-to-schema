<?php

namespace LaravelRulesToSchema\Parsers;

use FluentJsonSchema\FluentSchema;
use LaravelRulesToSchema\Contracts\RuleParser;
use LaravelRulesToSchema\RuleCategory;

class ExcludedParser implements RuleParser
{
    public function __invoke(string $property, FluentSchema $schema, array $validationRules, array $nestedRuleset): array|FluentSchema|null
    {
        foreach ($validationRules as $ruleArgs) {
            [$rule, $args] = $ruleArgs;

            if (is_string($rule) && in_array($rule, RuleCategory::excluded())) {
                return null;
            }
        }

        return $schema;
    }
}
