<?php

namespace LaravelRulesToSchema\Tests\Fixtures;

enum TestStringBackedEnum: string
{
    case One = 'one';
    case Two = 'two';
    case Three = 'three';
}
