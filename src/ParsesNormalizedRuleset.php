<?php

namespace LaravelRulesToSchema;

use FluentJsonSchema\FluentSchema;
use Mockery\Exception;

trait ParsesNormalizedRuleset
{
    public function parseRuleset(string $name, array $nestedRuleset): null|FluentSchema|array
    {
        $validationRules = $nestedRuleset[config('rules-to-schema.validation_rule_token')] ?? [];

        $schemas = [$name => FluentSchema::make()];

        foreach(config('rules-to-schema.pipes') as $pipeClass) {
            $instance = app($pipeClass);

            if (!$instance instanceof \LaravelRulesToSchema\Contracts\RuleParser) {
                throw new Exception("Rule parsers must implement " . \LaravelRulesToSchema\Contracts\RuleParser::class);
            }

            $newSchemas = [];

            foreach($schemas as $schemaKey => $schema) {
                $resultSchema = $instance($schemaKey, $schema, $validationRules, $nestedRuleset);

                if ($resultSchema === null) {
                    continue;
                }

                if (is_array($resultSchema)) {
                    $newSchemas = [...$newSchemas, ...$resultSchema];
                } else {
                    $newSchemas[$schemaKey] = $resultSchema;
                }
            }

            $schemas = $newSchemas;
        }

        if (count($schemas) == 0) {
            return null;
        } elseif (count($schemas) == 1) {
            return array_values($schemas)[0];
        }

        return $schemas;
    }
}
