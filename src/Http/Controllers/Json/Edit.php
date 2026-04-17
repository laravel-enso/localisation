<?php

namespace LaravelEnso\Localisation\Http\Controllers\Json;

use Illuminate\Support\Facades\App;
use LaravelEnso\Helpers\Services\JsonReader;
use LaravelEnso\Localisation\Models\Language;

class Edit
{
    public function __invoke(Language $language): string
    {
        $filename = App::langPath("{$language->name}.json");

        return (new JsonReader($filename))->json();
    }
}
