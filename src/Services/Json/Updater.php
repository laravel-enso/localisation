<?php

namespace LaravelEnso\Localisation\Services\Json;

use LaravelEnso\Localisation\Models\Language;

class Updater extends Handler
{
    public function __construct(
        private Language $language,
        private array $langArray,
        private ?string $subDir = null
    ) {
    }

    public function run()
    {
        $this->savePartial($this->language->name, $this->langArray, $this->subDir);

        $this->extraLangs()
            ->each(fn ($locale) => $this->updateDifferences($locale));
    }

    private function updateDifferences(string $locale)
    {
        $extraLang = $this->langFile($locale, $this->subDir);
        $updated = $this->syncKeys($this->langArray, $extraLang);

        if ($updated !== $extraLang) {
            $this->savePartial($locale, $updated, $this->subDir);
        }
    }

    private function extraLangs()
    {
        return Language::extra()
            ->where('id', '<>', $this->language->id)
            ->pluck('name');
    }
}
