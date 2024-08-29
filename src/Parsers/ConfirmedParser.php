<?php

namespace LaravelRulesToSchema\Parsers;

use FluentJsonSchema\FluentSchema;
use LaravelRulesToSchema\Contracts\RuleParser;

class ConfirmedParser implements RuleParser
{
    public function __invoke(string $attribute, FluentSchema $schema, array $validationRules, array $nestedRuleset): array|FluentSchema|null
    {
        foreach ($validationRules as $ruleArgs) {
            [$rule, $args] = $ruleArgs;

            if ($rule === 'confirmed') {
                return [
                    $attribute => $schema,
                    "{$attribute}_confirmed" => clone $schema,
                ];
            }
        }

        return $schema;
    }
}
