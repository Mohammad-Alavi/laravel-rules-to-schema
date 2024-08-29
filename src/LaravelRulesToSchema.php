<?php

namespace LaravelRulesToSchema;

use FluentJsonSchema\FluentSchema;

class LaravelRulesToSchema
{
    use ParsesNormalizedRuleset;

    public function parse(array $rules): FluentSchema
    {
        $normalizedRules = (new ValidationRuleNormalizer($rules))->getRules();

        $schema = FluentSchema::make()
            ->type()->object()
            ->return();

        foreach($normalizedRules as $property => $rawRules) {
            $propertySchema = $this->parseRuleset($property, $rawRules);

            if ($propertySchema instanceof FluentSchema) {
                $schema->object()->property($property, $propertySchema);
            } elseif (is_array($propertySchema)) {
                $schema->object()->properties($propertySchema);
            }
        }

        return $schema;
    }
}
