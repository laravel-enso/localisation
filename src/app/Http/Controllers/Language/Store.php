<?php

namespace LaravelEnso\Localisation\app\Http\Controllers\Language;

use Illuminate\Routing\Controller;
use LaravelEnso\Localisation\app\Http\Requests\ValidateLanguageRequest;
use LaravelEnso\Localisation\app\Services\Storer;

class Store extends Controller
{
    public function __invoke(ValidateLanguageRequest $request)
    {
        $language = (new Storer($request->validated()))->create();

        return [
            'message' => __('The language was successfully created'),
            'redirect' => 'system.localisation.edit',
            'param' => ['language' => $language->id],
        ];
    }
}
