<?php

namespace LaravelEnso\Localisation\app\Http\Controllers;

use Illuminate\Http\Request;
use LaravelEnso\Localisation\app\Models\Language;
use LaravelEnso\Localisation\app\Classes\Json\Merger;
use LaravelEnso\Localisation\app\Classes\Json\Reader;
use LaravelEnso\Localisation\app\Classes\Json\Updater;
use LaravelEnso\Localisation\app\Http\Requests\ValidateKeyRequest;

class JsonFileController
{
    public function index()
    {
        return Language::extra()
            ->get(['display_name', 'id'])
            ->map(function ($locale) {
                $locale->name = $locale->display_name;
                unset($locale->display_name);

                return $locale;
            });
    }

    public function edit(Language $language, string $subDir)
    {
        return (new Reader($language, $subDir))->get();
    }

    public function update(Request $request, Language $language, string $subDir)
    {
        (new Updater(
            $language,
            $request->get('langFile'),
            $subDir
        ))->run();

        return [
            'message' => __('The language files were successfully updated'),
        ];
    }

    public function addKey(ValidateKeyRequest $request)
    {
        $keys = collect($request->get('keys'))
            ->reduce(function ($keys, $key) {
                $keys[$key] = '';

                return $keys;
            }, []);

        (new Updater(new Language, $keys))
            ->addKey();

        return [
            'message' => __('The translation key was successfully added'),
        ];
    }

    public function merge($locale = null)
    {
        (new Merger())
            ->run($locale);

        return [
            'message' => __('The language files were successfully merged'),
        ];
    }
}
