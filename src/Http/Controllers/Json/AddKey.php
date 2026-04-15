<?php

namespace LaravelEnso\Localisation\Http\Controllers\Json;

use Illuminate\Support\Collection;
use LaravelEnso\Localisation\Http\Requests\ValidateKey;
use LaravelEnso\Localisation\Services\Json\KeyAdder;

class AddKey
{
    public function __invoke(ValidateKey $request)
    {
        $keys = Collection::wrap($request->get('keys'))
            ->mapWithKeys(fn ($key) => [$key => null])
            ->toArray();

        (new KeyAdder($keys))->run();

        return ['message' => __('The translation key was successfully added')];
    }
}
