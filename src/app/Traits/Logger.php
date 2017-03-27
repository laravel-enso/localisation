<?php

namespace LaravelEnso\Localisation\app\Traits;

trait Logger
{
    private function logFileName($file)
    {
        \Log::notice($file);
        \Log::notice(str_repeat('=', strlen($file)));
    }

    private function logMigration($translation, $newTranslation)
    {
        \Log::info($translation.' => The new label is: '.$newTranslation);
    }

    public function logMissingKeys($missingKeys)
    {
        \Log::notice(count($missingKeys).' missing translations:');

        foreach ($missingKeys as $key) {
            \Log::warning($key);
        }
    }

    public function logExtraKeys($extraKeys)
    {
        \Log::notice(count($extraKeys).' extra translations:');

        foreach ($extraKeys as $key) {
            \Log::warning($key);
        }
    }

    private function logError($error)
    {
        \Log::error($error);
    }
}
