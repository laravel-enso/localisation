<?php

namespace LaravelEnso\Localisation\app\Http\Requests;

use Illuminate\Validation\Rule;

class ValidateLanguageUpdate extends ValidateLanguageStore
{
    protected function nameUnique()
    {
        return Rule::unique('languages', 'name')
            ->ignore($this->route('language')->id);
    }

    protected function displayNameUnique()
    {
        return Rule::unique('languages', 'display_name')
            ->ignore($this->route('language')->id);
    }
}
