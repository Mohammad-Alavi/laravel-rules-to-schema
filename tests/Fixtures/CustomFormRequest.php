<?php

namespace LaravelRulesToSchema\Tests\Fixtures;

use Illuminate\Foundation\Http\FormRequest;

class CustomFormRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'field1' => ['required', 'string'],
        ];
    }
}
