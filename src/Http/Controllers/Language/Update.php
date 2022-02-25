<?php

namespace LaravelEnso\Localisation\Http\Controllers\Language;

use Illuminate\Routing\Controller;
use LaravelEnso\Localisation\Http\Requests\ValidateLanguage;
use LaravelEnso\Localisation\Models\Language;
use LaravelEnso\Localisation\Services\Updater;

class Update extends Controller
{
    public function __invoke(ValidateLanguage $request, Language $language)
    {
        (new Updater(
            $language,
            $request->validatedExcept('flag_sufix'),
            $request->get('flag_sufix')
        ))->run();

        return ['message' => __('The language was successfully updated')];
    }
}
