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
        $localisation = $this->route('localisation');
        $nameUnique = Rule::unique('languages', 'name');
        $displayNameUnique = Rule::unique('languages', 'display_name');

        if ($this->method() === 'PATCH') {
            $nameUnique = $nameUnique->ignore($localisation->id);
            $displayNameUnique = $displayNameUnique->ignore($localisation->id);
        }

        return [
            'name' => ['required', $nameUnique],
            'display_name' => ['required', $displayNameUnique],
            'flag_sufix' => 'required|string|size:2',
            'is_active' => 'boolean',
        ];
    }
}
