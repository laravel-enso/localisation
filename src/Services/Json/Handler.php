<?php

namespace LaravelEnso\Localisation\Services\Json;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use LaravelEnso\Helpers\Services\JsonReader;
use LaravelEnso\Localisation\Models\Language;
use LaravelEnso\Localisation\Services\SanitizeAppKeys;
use LaravelEnso\Localisation\Services\Traits\JsonFilePathResolver;

abstract class Handler
{
    use JsonFilePathResolver;

    protected function newTranslations(array $array): Collection
    {
        return Collection::wrap($array)->keys()
            ->mapWithKeys(fn ($key) => [$key => null]);
    }

    protected function saveMerged(string $locale, array $langFile): void
    {
        $this->saveToDisk($locale, $langFile);
    }

    protected function savePartial(string $locale, array $langFile, string $subDir): void
    {
        $this->saveToDisk($locale, $langFile, $subDir);
    }

    protected function saveToDisk(string $locale, array $langFile, ?string $subDir = null): void
    {
        File::put(
            $this->jsonFileName($locale, $subDir),
            json_encode($langFile, JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE | ($subDir ? JSON_PRETTY_PRINT : 0))
        );
    }

    protected function langFile(string $locale, string $subDir): array
    {
        return (new JsonReader($this->jsonFileName($locale, $subDir)))->array();
    }

    protected function syncKeys(array $source, array $target): array
    {
        $sourceKeys = Collection::wrap($source)->keys();
        $target = Collection::wrap($target)
            ->reject(fn ($value, $key) => ! $sourceKeys->contains($key));

        return $this->appendMissingKeys($source, $target->toArray());
    }

    protected function appendMissingKeys(array $source, array $target): array
    {
        $keysToAdd = Collection::wrap($source)->diffKeys($target);
        $newTranslations = $this->newTranslations($keysToAdd->all());

        return Collection::wrap($newTranslations)
            ->merge($target)
            ->sortKeys()
            ->toArray();
    }

    protected function merge(?string $locale = null): void
    {
        Language::extra()
            ->when($locale, fn ($languages) => $languages->where('name', $locale))
            ->pluck('name')
            ->each(fn ($locale) => $this->mergeLocale($locale));
    }

    private function mergeLocale(string $locale): void
    {
        $core = $this->langFile($locale, 'enso');

        $app = $this->getOrCreateApp($locale);

        $sanitizedApp = (new SanitizeAppKeys($app, $core))->sanitize($locale);

        $this->saveMerged($locale, array_merge($core, $sanitizedApp));
    }

    private function getOrCreateApp(string $locale): array
    {
        if (!File::exists($this->appJsonFileName($locale))) {
            File::copy(
                $this->appJsonFileName(Language::extra()->first()->name),
                $this->appJsonFileName($locale)
            );
        }

        return $this->langFile($locale, 'app');
    }
}
