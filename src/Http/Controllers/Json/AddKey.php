<?php

namespace LaravelEnso\Localisation\Http\Controllers\Json;

use Illuminate\Support\Collection;
use LaravelEnso\Localisation\Http\Requests\ValidateKey;
use LaravelEnso\Localisation\Services\Json\AddKey as Service;

class AddKey
{
    public function __invoke(ValidateKey $request): array
    {
        $keys = Collection::wrap($request->input('keys'))
            ->mapWithKeys(fn ($key) => [$key => null])
            ->toArray();

        (new Service($keys))->handle();

        return ['message' => __('The translation key was successfully added')];
    }
}
