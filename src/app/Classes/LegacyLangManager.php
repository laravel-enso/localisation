<?php

namespace LaravelEnso\Localisation\app\Classes;

class LegacyLangManager
{
    public function createLocale(string $locale)
    {
        $defaultLocale = config('app.fallback_locale');
        $files = collect(\File::files(resource_path('lang').'/'.$defaultLocale));
        \File::makeDirectory(resource_path('lang').'/'.$locale);

        $files->each(function($file) use ($locale) {
            $this->create($file, $locale);
        });
    }

    private function create($file, $locale)
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
