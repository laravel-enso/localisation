<?php

namespace LaravelEnso\Localisation\app\Commands;

use Illuminate\Console\Command;
use LaravelEnso\Localisation\app\Classes\CodeFilesManager;
use LaravelEnso\Localisation\app\Classes\LangFilesManager;
use LaravelEnso\Localisation\app\Classes\LocaleManager;
use LaravelEnso\Localisation\app\Traits\Logger;
use LaravelEnso\Localisation\app\Validation\ValidateLegacyTranslation;

class Scan extends Command
{
    use Logger;

    protected $signature = 'localisation:scan {folders*} {--M|migrate}';
    protected $description = "Collect localisation labels used with 'trans' in given directories and migrate
        to the L5.4 localisation format. Will output verbose result in laravel.log";

    private $locale;
    private $langFiles;
    private $codeFiles;

    private $validator;
    private $notProcessableCount = 0;
    private $processableCount = 0;

    public function __construct()
    {
        parent::__construct();

        $this->locale = config('app.fallback_locale');
        $this->langFiles = new LangFilesManager($this->locale);
        $this->validator = new ValidateLegacyTranslation();
    }

    public function handle()
    {
        $this->codeFiles = new CodeFilesManager($this->argument('folders'));

        echo "\nScanning "
            .count($this->codeFiles->files)
            .' files for translations in '
            .count($this->arguments()['folders'])
            ." folder(s)...\n";

        $this->analyzeTranslations();

        $this->renameLangFiles();

        echo($this->processableCount + $this->notProcessableCount)." translations found\n".($this->option('migrate') ? $this->processableCount." translations were processed\n".$this->notProcessableCount." were ignored\nCheck laravel.log for details\n" :
                $this->notProcessableCount." are not processable (will be ignored)\nCheck laravel.log for details\n");
    }

    private function analyzeTranslations()
    {
        foreach ($this->codeFiles->legacyTranslations as $legacyTranslations) {
            $this->logFileName($legacyTranslations['file']);

            $fileContent = \File::get($legacyTranslations['file']);
            $fileContent = $this->replaceTranslations($fileContent, $legacyTranslations['translations']);

            if ($this->option('migrate')) {
                \File::put($legacyTranslations['file'], $fileContent);
            }
        }
    }

    private function replaceTranslations($fileContent, $translations)
    {
        foreach ($translations as $translation) {
            if (!$this->validator->isProcessable($translation) || !$this->legacyTranslationHasMatch($translation)) {
                $this->notProcessableCount++;

                continue;
            }

            $this->processableCount++;

            $newTranslation = $this->replaceTranslation($translation);
            $fileContent = str_replace($translation, $newTranslation, $fileContent);

            $this->logMigration($translation, $newTranslation);
        }

        return $fileContent;
    }

    private function legacyTranslationHasMatch($translation)
    {
        $label = str_replace('trans', '', $translation);
        $label = substr($label, 2, strlen($label) - 4);
        $isProcessable = array_key_exists($label, $this->langFiles->translations);

        if (!$isProcessable) {
            $this->logError($translation.' => '.'The label is missing from Lang Files');
        }

        return $isProcessable;
    }

    private function replaceTranslation($translation)
    {
        $legacyKey = substr($translation, 7, strlen($translation) - 9);
        $newTranslation = str_replace('trans(', '__(', $translation);
        $newTranslation = $this->forceDoubleQuotes($newTranslation);
        $newKey = $this->langFiles->translations[$legacyKey];
        $newTranslation = str_replace($legacyKey, $newKey, $newTranslation);

        return $newTranslation;
    }

    private function forceDoubleQuotes($translation)
    {
        $length = strlen($translation);
        if ($translation[3] === "'" && $translation[$length - 2] === "'") {
            $translation[3] = '"';
            $translation[$length - 2] = '"';
        }

        return $translation;
    }

    private function renameLangFiles()
    {
        if ($this->option('migrate')) {
            $localeManager = new LocaleManager();

            foreach ($localeManager->locales as $locale) {
                $langFiles = new LangFilesManager($locale);

                foreach ($langFiles->files as $file) {
                    \File::move(resource_path('lang/'.$locale.'/'.$file),
                        resource_path('lang/'.$locale.'/'.$file.'.old'));
                }

                echo count($langFiles->files).' files renamed to *.old in lang/'.$locale.'/'."directory \n";
            }
        }
    }
}
