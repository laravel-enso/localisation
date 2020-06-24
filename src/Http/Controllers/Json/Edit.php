<?php

namespace LaravelEnso\Localisation\Http\Controllers\Json;

use LaravelEnso\Localisation\Models\Language;
use LaravelEnso\Localisation\Services\Json\Reader;

class Edit
{
    public function __invoke(Language $language, string $subDir)
    {
        return (new Reader($language, $subDir))->get();
    }
}
