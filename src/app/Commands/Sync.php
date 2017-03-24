<?php

namespace LaravelEnso\Localisation\App\Commands;

use Illuminate\Console\Command;
use LaravelEnso\Localisation\App\Classes\CodeFilesManager;
use LaravelEnso\Localisation\App\Classes\LocaleManager;
use LaravelEnso\Localisation\App\Traits\Logger;
use LaravelEnso\Localisation\App\Validation\ValidateNewTranslation;

class Sync extends Command
{
    use Logger;

    protected $signature = 'localisation:sync {folders*} {--U|update}';
    protected $description = "Finds missing and unused translations.
        Allows the synchronization of the app's legacy translations with the json files.
        Will output verbose result in laravel.log";

    private $codeFiles;
    private $localeManager;
    private $validator;

    public function __construct()
    {
        parent::__construct();

        $this->localeManager = new LocaleManager();
        $this->validator = new ValidateNewTranslation();
    }

    public function handle()
    {
        $this->codeFiles = new CodeFilesManager($this->argument('folders'));

        foreach ($this->localeManager->extraLocales as $locale) {
            $jsonTranslations = $this->getTranslationsFromJson($locale);

            $this->getDifferences($jsonTranslations);

            echo 'Sync report for locale: '.$locale."\n"
            .count($this->missingKeys)
            ." missing translations were found\n"
            .count($this->extraKeys)
                ." extra translations were found\nCheck laravel.log for details\n";

            $this->logOutput = PHP_EOL.'Sync report for locale: '.$locale.PHP_EOL.str_repeat('=', 26).PHP_EOL;
            $this->logMissingKeys($this->missingKeys);
            $this->logExtraKeys($this->extraKeys);
        }
    }

    private function getTranslationsFromJson($locale)
    {
        return (array) json_decode(\File::get(resource_path('lang/'.$locale.'.json')));
    }

    private function getDifferences($jsonTranslations)
    {
        $jsonKeys = array_keys($jsonTranslations);
        $this->missingKeys = array_diff($this->codeFiles->newTranslations, $jsonKeys);
        $this->extraKeys = array_diff($jsonKeys, $this->codeFiles->newTranslations);
    }
}
