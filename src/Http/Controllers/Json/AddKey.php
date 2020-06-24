<?php

namespace LaravelEnso\Localisation\Http\Controllers\Json;

use Illuminate\Support\Collection;
use LaravelEnso\Localisation\Http\Requests\ValidateKeyRequest;
use LaravelEnso\Localisation\Models\Language;
use LaravelEnso\Localisation\Services\Json\Updater;

class AddKey
{
    public function __invoke(ValidateKeyRequest $request, Language $language)
    {
        $keys = (new Collection($request->get('keys')))
            ->mapWithKeys(fn ($key) => [$key => null])
            ->toArray();

        (new Updater($language, $keys))->addKey();

        return ['message' => __('The translation key was successfully added')];
    }
}
