<?php

namespace LaravelRulesToSchema\Contracts;

use FluentJsonSchema\FluentSchema;

interface HasJsonSchema
{
    public function toJsonSchema(string $attribute): FluentSchema;
}
