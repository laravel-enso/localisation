<?php

namespace LaravelEnso\Localisation\app\Http\Controllers;

use Illuminate\Http\Request;
use LaravelEnso\Localisation\app\Models\Language;
use LaravelEnso\Localisation\app\Handlers\Json\Reader;
use LaravelEnso\Localisation\app\Handlers\Json\Updater;

class JsonFileController
{
    public function index()
    {
        $locales = Language::extra()->get(['display_name', 'id']);

        $locales = $locales->map(function ($locale) {
            $locale->name = $locale->display_name;
            unset($locale->display_name);

            return $locale;
        });

        return compact('locales');
    }

    public function edit(Language $language)
    {
        return (new Reader($language))->content();
    }

    public function update(Request $request, Language $language)
    {
        (new Updater($language, $request->get('langFile')))->run();

        return ['message' => __(config('enso.labels.successfulOperation'))];
    }

    public function add(Request $request)
    {
        $data = [$request->get('langKey') => ''];
        (new Updater(new Language, $data))->add();

        return ['message' => __(config('enso.labels.successfulOperation'))];
    }
}
