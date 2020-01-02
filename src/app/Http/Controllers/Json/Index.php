<?php

namespace LaravelEnso\Localisation\App\Http\Controllers\Json;

use LaravelEnso\Localisation\App\Models\Language;

class Index
{
    public function __invoke()
    {
        return Language::extra()->get()->map(fn ($locale) => [
            'id' => $locale->id,
            'name' => $locale->display_name,
        ]);
    }
}
