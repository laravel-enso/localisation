<?php

namespace LaravelEnso\Localisation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ValidateLanguageRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required', $this->unique('name')],
            'display_name' => ['required', $this->unique('display_name')],
            'flag_sufix' => 'required|string|size:2',
            'is_rtl' => 'required|boolean',
            'is_active' => 'required|boolean',
        ];
    }

    protected function unique($attribute)
    {
        return Rule::unique('languages', $attribute)
            ->ignore(optional($this->route('language'))->id);
    }
}
