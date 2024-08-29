<?php

namespace LaravelRulesToSchema\Parsers;

use FluentJsonSchema\FluentSchema;
use LaravelRulesToSchema\Contracts\RuleParser;

class RequiredParser implements RuleParser
{
    public function __invoke(string $attribute, FluentSchema $schema, array $validationRules, array $nestedRuleset): array|FluentSchema|null
    {
        $foundRequired = false;
        foreach ($validationRules as $ruleArgs) {
            [$rule, $args] = $ruleArgs;

            if (! is_string($rule)) {
                continue;
            }

            if ($rule === 'sometimes') {
                return $schema;
            } elseif ($rule == 'required') {
                $foundRequired = true;
            }
        }

        if ($foundRequired) {
            $schema->object()->required();
        }

        return $schema;
    }
}
