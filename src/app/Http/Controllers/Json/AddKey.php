<?php

namespace LaravelEnso\Localisation\app\Http\Controllers\Json;

use LaravelEnso\Localisation\app\Models\Language;
use LaravelEnso\Localisation\app\Services\Json\Updater;
use LaravelEnso\Localisation\app\Http\Requests\ValidateKeyRequest;

class AddKey
{
    public function __invoke(ValidateKeyRequest $request)
    {
        $keys = collect($request->get('keys'))
            ->reduce(function ($keys, $key) {
                $keys[$key] = '';

                return $keys;
            }, []);

        (new Updater(new Language, $keys))->addKey();

        return [
            'message' => __('The translation key was successfully added'),
        ];
    }
}
