<?php

namespace LaravelEnso\Localisation\app\Commands;

use Illuminate\Console\Command;
use LaravelEnso\Localisation\app\Classes\LangFilesManager;
use LaravelEnso\Localisation\app\Classes\LocaleManager;

class Generate extends Command
{
    protected $signature = 'localisation:generate';
    protected $description = 'Generates new L5.4 Json translations files from resource/lang/* files.
        Uses default locale keys as base.';

    public $localeManager;
    public $defaultLangFiles;
    public $jsonContent;

    public function __construct()
    {
        parent::__construct();

        $this->localeManager = new LocaleManager();
        $this->defaultLangFiles = new LangFilesManager($this->localeManager->defaultLocale);
    }

    public function handle()
    {
        echo "\nWarning: Ignoring ".implode(', ', $this->defaultLangFiles->excludedFiles)." files\n";

        foreach ($this->localeManager->extraLocales as $locale) {
            $jsonContent = $this->generateJsonContent($locale);

            $this->createJsonFile($jsonContent, $locale);
        }
    }

    private function generateJsonContent($locale)
    {
        $matchingKeys = [];

        $langFile = new LangFilesManager($locale);

        foreach ($this->defaultLangFiles->translations as $key => $value) {
            $matchingKeys[$value] = isset($langFile->translations[$key]) ? $langFile->translations[$key] : null;
        }

        return json_encode($matchingKeys);
    }

    private function createJsonFile($jsonContent, $locale)
    {
        $file = resource_path('lang/'.$locale.'.json');

        \File::put($file, $jsonContent);

        echo $file." was generated.\n";
    }
}
