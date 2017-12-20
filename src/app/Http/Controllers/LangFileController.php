<?php

namespace LaravelEnso\Localisation\app\Http\Controllers;

use Illuminate\Http\Request;
use LaravelEnso\Localisation\app\Models\Language;
use LaravelEnso\Localisation\app\Classes\JsonLangManager;

class LangFileController
{
    public function getLangFile(Language $language, JsonLangManager $service)
    {
        return response()->json($service->getContent($language->name));
    }

    public function editTexts()
    {
        $locales = Language::extra()->pluck('display_name', 'id');

        return compact('locales');
    }

    public function saveLangFile(Request $request, Language $language, JsonLangManager $service)
    {
        return $service->update($request->get('langFile'), $language->name);
    }
}
