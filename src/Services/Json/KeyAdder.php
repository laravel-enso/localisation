<?php

namespace LaravelEnso\Localisation\Services\Json;

use LaravelEnso\Localisation\Models\Language;

class KeyAdder extends Handler
{
    public function __construct(private array $keys)
    {
    }

    public function run(): void
    {
        Language::extra()->pluck('name')
            ->each(fn ($locale) => $this->addToLocale($locale));
    }

    private function addToLocale(string $locale): void
    {
        $langFile = $this->langFile($locale, $this->updateDir());
        $updated = $this->appendMissingKeys($this->keys, $langFile);

        if ($updated !== $langFile) {
            $this->savePartial($locale, $updated, $this->updateDir());
        }
    }
}
