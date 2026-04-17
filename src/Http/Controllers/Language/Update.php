<?php

namespace LaravelEnso\Localisation\Http\Controllers\Language;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use LaravelEnso\Localisation\Http\Requests\ValidateLanguage;
use LaravelEnso\Localisation\Models\Language;

class Update extends Controller
{
    public function __invoke(ValidateLanguage $request, Language $language)
    {
        DB::transaction(fn () => $language->update($request->validated()));

        return ['message' => __('The language was successfully updated')];
    }
}
