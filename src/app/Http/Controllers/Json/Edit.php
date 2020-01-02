<?php

namespace LaravelEnso\Localisation\App\Http\Controllers\Json;

use LaravelEnso\Localisation\App\Models\Language;
use LaravelEnso\Localisation\App\Services\Json\Reader;

class Edit
{
    public function __invoke(Language $language, string $subDir)
    {
        return (new Reader($language, $subDir))->get();
    }
}
