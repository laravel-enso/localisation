<?php

namespace LaravelEnso\Localisation\app\Http\Controllers\Json;

use LaravelEnso\Localisation\app\Models\Language;
use LaravelEnso\Localisation\app\Services\Json\Reader;

class Edit
{
    public function __invoke(Language $language, string $subDir)
    {
        return (new Reader($language, $subDir))->get();
    }
}
