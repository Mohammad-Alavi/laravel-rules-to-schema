<?php

namespace LaravelRulesToSchema;

use FluentJsonSchema\FluentSchema;
use LaravelRulesToSchema\Contracts\RuleParser;
use Mockery\Exception;

trait ParsesNormalizedRuleset
{
    public function parseRuleset(string $name, array $nestedRuleset): null|FluentSchema|array
    {
        $validationRules = $nestedRuleset[config('rules-to-schema.validation_rule_token')] ?? [];

        $schemas = [$name => FluentSchema::make()];

        foreach (\LaravelRulesToSchema\Facades\LaravelRulesToSchema::getParsers() as $parserClass) {
            $instance = app($parserClass);

            if (! $instance instanceof RuleParser) {
                throw new Exception('Rule parsers must implement '.RuleParser::class);
            }

            $newSchemas = [];

            foreach ($schemas as $schemaKey => $schema) {
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
