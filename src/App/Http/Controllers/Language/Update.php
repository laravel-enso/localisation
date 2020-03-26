<?php

namespace LaravelEnso\Localisation\App\Http\Controllers\Language;

use Illuminate\Routing\Controller;
use LaravelEnso\Localisation\App\Http\Requests\ValidateLanguageRequest;
use LaravelEnso\Localisation\App\Models\Language;
use LaravelEnso\Localisation\App\Services\Updater;

class Update extends Controller
{
    public function __invoke(ValidateLanguageRequest $request, Language $language)
    {
        (new Updater($language, $request->validated()))->run();

        return ['message' => __('The language was successfully updated')];
    }
}
