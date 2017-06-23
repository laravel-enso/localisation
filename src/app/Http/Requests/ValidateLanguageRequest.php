<?php

namespace LaravelEnso\Localisation\app\Http\Requests;

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
        $localisation = $this->route('localisation');
        $nameUnique = Rule::unique('languages', 'name');
        $displayNameUnique = Rule::unique('languages', 'display_name');
        $flagUnique = Rule::unique('languages', 'flag');

        if ($this->_method == 'PATCH') {
            $nameUnique = $nameUnique->ignore($localisation->id);
            $displayNameUnique = $displayNameUnique->ignore($localisation->id);
            $flagUnique = $flagUnique->ignore($localisation->id);
        }

        return [
            'name'         => ['required', $nameUnique],
            'display_name' => ['required', $displayNameUnique],
            'flag'         => ['required', $flagUnique],
        ];
    }
}
