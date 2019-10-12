<?php

namespace LaravelEnso\Localisation\app\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ValidateLanguageRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required', $this->nameUnique()],
            'display_name' => ['required', $this->displayNameUnique()],
            'flag_sufix' => 'required|string|size:2',
            'is_rtl' => 'required|boolean',
            'is_active' => 'required|boolean',
        ];
    }

    protected function nameUnique()
    {
        return Rule::unique('languages', 'name')
            ->ignore(optional($this->route('language'))->id);
    }

    protected function displayNameUnique()
    {
        return Rule::unique('languages', 'display_name')
            ->ignore(optional($this->route('language'))->id);
    }
}
