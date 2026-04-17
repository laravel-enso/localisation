<?php

namespace LaravelEnso\Localisation\Http\Controllers\Language;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use LaravelEnso\Localisation\Http\Requests\ValidateLanguage;
use LaravelEnso\Localisation\Models\Language;

class Store extends Controller
{
    public function __invoke(ValidateLanguage $request, Language $language)
    {
        $language = DB::transaction(fn () => Language::create($request->validated()));

        return [
            'message' => __('The language was successfully created'),
            'redirect' => 'system.localisation.edit',
            'param' => ['language' => $language->id],
        ];
    }
}
