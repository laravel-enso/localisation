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

        if ($this->_method == 'PATCH') {
            $nameUnique = $this->_method == 'PATCH' ? $nameUnique->ignore($localisation->id) : $nameUnique;
        }

        return [
            'name'         => [ 'required', $nameUnique ],
            'display_name' => 'required',
            'flag'         => 'required',
        ];
    }
}
