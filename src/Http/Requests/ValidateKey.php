<?php

namespace LaravelEnso\Localisation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateKey extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return ['keys' => 'required|array'];
    }
}
