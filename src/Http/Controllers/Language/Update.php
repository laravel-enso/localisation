<?php

namespace LaravelEnso\Localisation\Http\Controllers\Language;

use Illuminate\Routing\Controller;
use LaravelEnso\Localisation\Http\Requests\ValidateLanguageRequest;
use LaravelEnso\Localisation\Models\Language;
use LaravelEnso\Localisation\Services\Updater;

class Update extends Controller
{
    public function __invoke(ValidateLanguageRequest $request, Language $language)
    {
        (new Updater($language, $request->validated()))->run();

        return ['message' => __('The language was successfully updated')];
    }
}
