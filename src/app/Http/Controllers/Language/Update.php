<?php

namespace LaravelEnso\Localisation\app\Http\Controllers\Language;

use Illuminate\Routing\Controller;
use LaravelEnso\Localisation\app\Http\Requests\ValidateLanguageRequest;
use LaravelEnso\Localisation\app\Models\Language;
use LaravelEnso\Localisation\app\Services\Updater;

class Update extends Controller
{
    public function __invoke(ValidateLanguageRequest $request, Language $language)
    {
        (new Updater($language, $request->validated()))->run();

        return [
            'message' => __('The language was successfully updated'),
        ];
    }
}
