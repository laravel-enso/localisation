<?php

namespace LaravelEnso\Localisation\Http\Controllers\Json;

use Illuminate\Http\Request;
use LaravelEnso\Localisation\Models\Language;
use LaravelEnso\Localisation\Services\Json\Updater;

class Update
{
    public function __invoke(Request $request, Language $language): array
    {
        (new Updater($language, $request->input('langFile')))->run();

        return ['message' => __('The language files were successfully updated')];
    }
}
