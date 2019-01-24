<?php

namespace LaravelEnso\Localisation\app\Classes\Json;

class Merger extends Handler
{
    public function run(string $locale = null)
    {
        $this->merge($locale);
    }
}
