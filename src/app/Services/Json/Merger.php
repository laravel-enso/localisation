<?php

namespace LaravelEnso\Localisation\app\Services\Json;

class Merger extends Handler
{
    public function run(?string $locale = null)
    {
        $this->merge($locale);
    }
}
