<?php

namespace LaravelEnso\Localisation\Http\Controllers\Language;

use Illuminate\Routing\Controller;
use LaravelEnso\Localisation\Http\Requests\ValidateLanguage;
use LaravelEnso\Localisation\Services\Storer;

class Store extends Controller
{
    public function __invoke(ValidateLanguage $request)
    {
        $language = (new Storer(
            $request->validatedExcept('flag_sufix'),
            $request->get('flag_sufix')
        ))->create();

        return [
            'message' => __('The language was successfully created'),
            'redirect' => 'system.localisation.edit',
            'param' => ['language' => $language->id],
        ];
    }
}
