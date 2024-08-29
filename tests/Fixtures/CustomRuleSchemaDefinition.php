<?php

namespace LaravelRulesToSchema\Tests\Fixtures;

use FluentJsonSchema\FluentSchema;
use LaravelRulesToSchema\Contracts\HasJsonSchema;

class CustomRuleSchemaDefinition implements HasJsonSchema
{
    public function toJsonSchema(string $attribute): FluentSchema
    {
        return FluentSchema::make()
            ->type()->array()
            ->items(FluentSchema::make()
                ->type()->object()
                ->property('property', FluentSchema::make()
                    ->type()->boolean()
                )
            )
            ->return();
    }
}
