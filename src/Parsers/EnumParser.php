<?php

namespace LaravelRulesToSchema\Parsers;

use FluentJsonSchema\FluentSchema;
use Illuminate\Validation\Rules\Enum as EnumRule;
use LaravelRulesToSchema\Contracts\RuleParser;
use ReflectionClass;

class EnumParser implements RuleParser
{
    public function __invoke(string $attribute, FluentSchema $schema, array $validationRules, array $nestedRuleset): array|FluentSchema|null
    {
        foreach ($validationRules as $ruleArgs) {
            [$rule, $args] = $ruleArgs;

            if ($rule instanceof EnumRule) {
                $enumType   = invade($rule)->type; /** @phpstan-ignore property.protected */
                $reflection = new ReflectionClass($enumType);

                if (count($reflection->getConstants()) > 0) {
                    $values = array_values(array_map(function (\UnitEnum|\BackedEnum $c) {
                        return $c instanceof \BackedEnum ? $c->value : $c->name;
                    }, $reflection->getConstants()));

                    $schema->object()->enum($values);
                }
            }
        }

        return $schema;
    }
}
