<?php

namespace LaravelEnso\Localisation\app\Classes;

class LangFilesManager
{
    public $excludedFiles = ['auth.php', 'pagination.php', 'passwords.php', 'validation.php'];

    public $locale;
    public $files;
    public $translations;

    public function __construct($locale)
    {
        $this->locale = $locale;
        $this->files = $this->getFiles();
        $this->translations = $this->getTranslations();
    }

    public function getFiles()
    {
        $files = \File::files(resource_path('lang/'.$this->locale));

        $files = array_map(function ($file) {
            return last(explode('/', $file));
        }, $files);

        return $this->removeExcludedFiles($files);
    }

    private function removeExcludedFiles($files)
    {
        $keysToUnset = array_keys(array_intersect($files, $this->excludedFiles));

        foreach ($keysToUnset as $key) {
            unset($files[$key]);
        }

        return $files;
    }

    public function getTranslations()
    {
        $translations = [];

        foreach ($this->files as $file) {
            $translations = array_merge($translations, $this->getTranslationsFromFile($file));
        }

        return $translations;
    }

    private function getTranslationsFromFile($file)
    {
        $fileContent = $this->getFileContent($file);
        $prefix = str_replace('.php', '', $file);
        $translations = $this->addPrefix($fileContent, $prefix);

        return $translations;
    }

    public function getFileContent($file)
    {
        $fileContent = include resource_path('lang/'.$this->locale.'/'.$file);

        return $fileContent;
    }

    private function addPrefix($fileContent, $prefix)
    {
        $newKeysArray = array_map(function ($key) use ($prefix) {
            return $prefix.'.'.$key;
        }, array_keys($fileContent));

        $result = array_combine($newKeysArray, array_values($fileContent));

        return $result;
    }
}
