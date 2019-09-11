<?php

namespace LaravelEnso\Localisation\app\Http\Controllers\Language;

use Illuminate\Routing\Controller;
use LaravelEnso\Localisation\app\Models\Language;
use LaravelEnso\Localisation\app\Services\Updater;
use LaravelEnso\Localisation\app\Http\Requests\ValidateLanguageUpdate;

class Update extends Controller
{
    public function __invoke(ValidateLanguageUpdate $request, Language $localisation)
    {
        (new Updater($localisation, $request->validated()))->run();

        return [
            'message' => __('The language was successfully updated'),
        ];
    }
}
