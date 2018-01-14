<?php

namespace LaravelEnso\Localisation\app\Classes;

use LaravelEnso\Localisation\app\Exceptions\LocalisationException;

class LegacyLangManager
{
    public function createLocale(string $locale)
    {
        $this->createDir($locale)
            ->createLangs($locale);
    }

    private function createDir(string $locale)
    {
        if (\File::isDirectory(resource_path('lang').DIRECTORY_SEPARATOR.$locale)) {
            throw new LocalisationException(__(
                "Can't create the language for locale :locale files because the legacy folder :folder already exists",
                ['locale' => $locale, 'folder' => resource_path('lang').DIRECTORY_SEPARATOR.$locale]
            ));
        }

        \File::makeDirectory(resource_path('lang').DIRECTORY_SEPARATOR.$locale);

        return $this;
    }

    private function createLangs($locale)
    {
        $defaultLocale = config('app.fallback_locale');
        $files = collect(\File::files(resource_path('lang').DIRECTORY_SEPARATOR.$defaultLocale));

        $files->each(function ($file) use ($locale) {
            $this->createLang($file, $locale);
        });
    }

    private function createLang($file, $locale)
    {
        $fileName = last(explode(DIRECTORY_SEPARATOR, $file));
        $content = \File::get($file);

        \File::put(
            resource_path('lang').DIRECTORY_SEPARATOR.$locale.DIRECTORY_SEPARATOR.$fileName,
            $content
        );
    }

    public function renameFolder($oldName, $newName)
    {
        return $oldName !== $newName
            ? \File::move(
                resource_path('lang'.DIRECTORY_SEPARATOR.$oldName),
                resource_path('lang'.DIRECTORY_SEPARATOR.$newName)
            )
            : false;
    }

    public function delete($locale)
    {
        \File::deleteDirectory(resource_path('lang'.DIRECTORY_SEPARATOR.$locale));
    }
}
