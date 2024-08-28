<?php

namespace LaravelRulesToSchema\Parsers;

use FluentJsonSchema\FluentSchema;
use LaravelRulesToSchema\Contracts\RuleParser;

class ConfirmedParser implements RuleParser
{
    public function __invoke(string $property, FluentSchema $schema, array $validationRules, array $nestedRuleset): array|FluentSchema|null
    {
        foreach ($validationRules as $ruleArgs) {
            [$rule, $args] = $ruleArgs;

            if ($rule === 'confirmed') {
                return [
                    $property => $schema,
                    "{$property}_confirmed" => clone $schema,
                ];
            }
        }

        return $schema;
    }
}
