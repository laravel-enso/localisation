<?php

namespace LaravelEnso\Localisation\Classes;

class LocaleManager
{
    public $defaultLocale;
    public $locales;
    public $extraLocales;

    public function __construct()
    {
        $this->defaultLocale = config('app.fallback_locale');
        $this->locales = $this->getLocales();
        $this->extraLocales = $this->getExtraLocales();
    }

    private function getLocales()
    {
        $locales = [];
        $fullPathLocales = \File::directories(resource_path('lang'));

        foreach ($fullPathLocales as $path) {
            $locales[] = $this->getLastChildDirectoryName($path);
        }

        return $locales;
    }

    private function getExtraLocales()
    {
        $extraLocales = $this->locales;
        $index = array_search($this->defaultLocale, $extraLocales);

        unset($extraLocales[$index]);

        return $extraLocales;
    }

    private function getLastChildDirectoryName($fullPathDirectory)
    {
        $fullPathArray = explode('/', $fullPathDirectory);

        return end($fullPathArray);
    }
}
