<?php

namespace LaravelEnso\Localisation\app\Http\Controllers;

use Illuminate\Http\Request;
use LaravelEnso\Localisation\app\Classes\JsonLangManager;
use LaravelEnso\Localisation\app\Models\Language;
use LaravelEnso\Select\app\Traits\SelectListBuilder;

class LangFileController
{
    use SelectListBuilder;

    private $request;
    private $jsonLang;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->jsonLang = new JsonLangManager();
    }

    public function getLangFile($locale)
    {
        return response()->json($this->jsonLang->getContent($locale));
    }

    public function editTexts()
    {
        $localisations = Language::extra()->pluck('display_name', 'name');
        $locales = $this->buildSelectList($localisations);

        return view('laravel-enso/localisation::editTexts', compact('locales'));
    }

    public function saveLangFile()
    {
        return $this->jsonLang->update($this->request->get('locale'), $this->request->get('langFile'));
    }
}
