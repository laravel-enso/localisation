<?php

namespace LaravelEnso\Localisation\app\Http\Controllers\Json;

use LaravelEnso\Localisation\app\Models\Language;

class Index
{
    public function __invoke()
    {
        return Language::extra()
            ->get(['display_name', 'id'])
            ->map(function ($locale) {
                $locale->name = $locale->display_name;
                unset($locale->display_name);

                return $locale;
            });
    }
}
