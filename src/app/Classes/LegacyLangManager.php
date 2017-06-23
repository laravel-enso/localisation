<?php

namespace LaravelEnso\Localisation\app\Classes;

class LegacyLangManager
{
    public function createLocale(string $locale)
    {
        $this->createDir($locale)
            ->createLangs($locale);
    }

    private function createDir(string $locale)
    {
        if (\File::isDirectory(resource_path('lang').'/'.$locale)) {
            throw new \EnsoException(__("Can't create the language files because legacy folder already exists".' '.resource_path('lang').'/'.$locale));
        }

        \File::makeDirectory(resource_path('lang').'/'.$locale);

        return $this;
    }

    private function createLangs($locale)
    {
        $defaultLocale = config('app.fallback_locale');
        $files = collect(\File::files(resource_path('lang').'/'.$defaultLocale));

        $files->each(function ($file) use ($locale) {
            $this->createLang($file, $locale);
        });
    }

    private function createLang($file, $locale)
    {
        $fileName = last(explode('/', $file));
        $content = \File::get($file);
        \File::put(resource_path('lang').'/'.$locale.'/'.$fileName, $content);
    }

    public function renameFolder($oldName, $newName)
    {
        return $oldName !== $newName
            ? \File::move(resource_path('lang/'.$oldName), resource_path('lang/'.$newName))
            : false;
    }

    public function delete($locale)
    {
        \File::deleteDirectory(resource_path('lang/'.$locale));
    }
}
