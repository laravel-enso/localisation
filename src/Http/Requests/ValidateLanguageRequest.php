<?php

namespace LaravelEnso\Localisation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateLanguageRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $localisation = $this->route('localisation');

        if ($this->_method == 'PATCH') {

            return [

                'name'         => 'required|unique:languages,name,' . $localisation->id . ',id',
                'display_name' => 'required',
                'flag'         => 'required',
            ];
        } else {

            return [

                'name'         => 'required|unique:languages',
                'display_name' => 'required',
                'flag'         => 'required',
            ];
        }
    }
}
