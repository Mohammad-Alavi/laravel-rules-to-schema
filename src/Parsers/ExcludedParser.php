<?php

namespace LaravelRulesToSchema\Parsers;

use FluentJsonSchema\FluentSchema;
use LaravelRulesToSchema\Contracts\RuleParser;

class ExcludedParser implements RuleParser
{
    public function __invoke(string $attribute, FluentSchema $schema, array $validationRules, array $nestedRuleset): array|FluentSchema|null
    {
        foreach ($validationRules as $ruleArgs) {
            [$rule, $args] = $ruleArgs;
            if (is_string($rule) && in_array($rule, config('rules-to-schema.rule_type_map.exclude'))) {
                return null;
            }
        }

        return $schema;
    }
}
