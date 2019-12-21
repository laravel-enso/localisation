<?php

namespace LaravelEnso\Localisation\app\Http\Controllers\Json;

use LaravelEnso\Localisation\app\Http\Requests\ValidateKeyRequest;
use LaravelEnso\Localisation\app\Models\Language;
use LaravelEnso\Localisation\app\Services\Json\Updater;

class AddKey
{
    public function __invoke(ValidateKeyRequest $request)
    {
        $keys = collect($request->get('keys'))
            ->mapWithKeys(fn($key) => [$key => ''])
            ->toArray();

        (new Updater(new Language(), $keys))->addKey();

        return [
            'message' => __('The translation key was successfully added'),
        ];
    }
}
