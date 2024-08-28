<?php

namespace LaravelRulesToSchema;

use FluentJsonSchema\FluentSchema;

class LaravelRulesToSchema {

    public function parse(array $rules): FluentSchema
    {
        return (new RuleParser($rules))->parse();
    }

}
