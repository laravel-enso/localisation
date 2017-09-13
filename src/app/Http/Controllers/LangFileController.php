<?php

namespace LaravelEnso\Localisation\app\Http\Controllers;

use Illuminate\Http\Request;
use LaravelEnso\Localisation\app\Classes\JsonLangManager;
use LaravelEnso\Localisation\app\Models\Language;

class LangFileController
{
    private $request;
    private $jsonLang;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->jsonLang = new JsonLangManager();
    }

    public function getLangFile(Language $language)
    {
        return response()->json($this->jsonLang->getContent($language->name));
    }

    public function editTexts()
    {
        $locales = Language::extra()->pluck('display_name', 'id');

        return view('laravel-enso/localisation::editTexts', compact('locales'));
    }

    public function saveLangFile(Language $language)
    {
        return $this->jsonLang->update($language->name, $this->request->get('langFile'));
    }
}