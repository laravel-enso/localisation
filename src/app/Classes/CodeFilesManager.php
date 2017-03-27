<?php

namespace LaravelEnso\Localisation\app\Classes;

class CodeFilesManager
{
    public $folders;
    public $files;
    public $legacyTranslations;
    public $newTranslations;

    public function __construct($folders)
    {
        $this->folders = $folders;
        $this->files = $this->getFiles();
        $this->legacyTranslations = $this->getLegacyTranslations();
        $this->newTranslations = $this->getNewTranslations();
    }

    public function getFiles()
    {
        $files = [];

        foreach ($this->folders as $folder) {
            $files = array_merge($files, \File::allFiles($folder));
        }

        return $files;
    }

    private function getLegacyTranslations()
    {
        $translations = [];

        foreach ($this->files as $file) {
            $fileContent = \File::get($file);
            $fileTranslations = $this->getLegacyTranslationsFromFile($fileContent);

            if (count($fileTranslations)) {
                $translations[] = ['file' => $file, 'translations' => $fileTranslations];
            }
        }

        return $translations;
    }

    private function getLegacyTranslationsFromFile($fileContent)
    {
        $index = 0;
        $translations = [];

        while ($startingPos = strpos($fileContent, 'trans(', $index)) {
            $endingPos = strpos($fileContent, ')', $startingPos);
            $label = substr($fileContent, $startingPos, $endingPos - $startingPos + 1);
            $index = $startingPos + 1;

            $translations[] = $label;
        }

        return $translations;
    }

    private function getNewTranslations()
    {
        $translations = [];

        foreach ($this->files as $file) {
            $fileContent = \File::get($file);
            $fileTranslations = $this->getNewTranslationsFromFile($fileContent);
            $translations = array_merge($translations, $fileTranslations);
        }

        $translations = array_unique($translations);

        return $translations;
    }

    private function getNewTranslationsFromFile($fileContent)
    {
        $index = 0;
        $translations = [];

        while ($startingPos = strpos($fileContent, '__(', $index)) {
            $endingPos = strpos($fileContent, ')', $startingPos);

            if ($endingPos - $startingPos < 5) {
                $index = $startingPos + 1;

                continue;
            }

            $label = substr($fileContent, $startingPos + 3, $endingPos - $startingPos - 3);

            if ($label[0] === $label[strlen($label) - 1] && in_array($label[0], ['"', '"'])) {
                $translations[] = substr($label, 1, strlen($label) - 2);
            }

            $index = $startingPos + 1;
        }

        return $translations;
    }
}
