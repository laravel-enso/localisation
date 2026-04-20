<?php

namespace LaravelEnso\Localisation\Services\Json;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use LaravelEnso\Helpers\Services\JsonReader;
use LaravelEnso\Localisation\Models\Language;

class Updater
{
    public function __construct(
        private Language $language,
        private array $langArray
    ) {
    }

    public function run(): void
    {
        SaveToDisk::handle($this->language->name, $this->langArray, overwrite: true);

        $this->extraLangs()
            ->each(fn (string $locale) => $this->updateDifferences($locale));
    }

    private function updateDifferences(string $locale): void
    {
        $langFile = $this->langFile($locale);
        $synced = $this->sync($langFile);

        if ($langFile !== $synced) {
            SaveToDisk::handle($locale, $synced, overwrite: true);
        }
    }

    private function sync(array $langFile): array
    {
        $synced = Collection::wrap($this->langArray)
            ->mapWithKeys(fn ($value, $key) => [$key => $langFile[$key] ?? null]);

        return $synced->union($langFile)->all();
    }

    private function langFile(string $locale): array
    {
        return File::exists(App::langPath("{$locale}.json"))
            ? (new JsonReader(App::langPath("{$locale}.json")))->array()
            : [];
    }

    private function extraLangs(): Collection
    {
        return Language::extra()
            ->where('name', '<>', $this->language->name)
            ->pluck('name');
    }
}
