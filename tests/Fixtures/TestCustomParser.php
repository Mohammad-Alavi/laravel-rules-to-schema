<?php

namespace LaravelRulesToSchema\Tests\Fixtures;

use FluentJsonSchema\FluentSchema;
use LaravelRulesToSchema\Contracts\RuleParser;

class TestCustomParser implements RuleParser
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(string $attribute, FluentSchema $schema, array $validationRules, array $nestedRuleset): array|FluentSchema|null
    {
        foreach ($validationRules as $ruleArgs) {
            [$rule, $args] = $ruleArgs;

            if ($rule == 'test_custom_parser') {
                return $schema->type()->array()
                    ->items(FluentSchema::make()
                        ->type()->integer()
                    )
                    ->return();
            }
        }

        return $schema;
    }
}
