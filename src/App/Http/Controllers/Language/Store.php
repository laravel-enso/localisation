<?php

namespace LaravelEnso\Localisation\App\Http\Controllers\Language;

use Illuminate\Routing\Controller;
use LaravelEnso\Localisation\App\Http\Requests\ValidateLanguageRequest;
use LaravelEnso\Localisation\App\Services\Storer;

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
