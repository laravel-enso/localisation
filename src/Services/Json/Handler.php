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

    protected function merge(?string $locale = null): void
    {
        $languages = Language::extra();

        if ($locale) {
            $languages->where('name', $locale);
        }

        $languages->pluck('name')
            ->each(fn ($locale) => $this->mergeLocale($locale));
    }

    private function mergeLocale(string $locale): void
    {
        $core = (new JsonReader($this->coreJsonFileName($locale)))->array();

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

        return (new JsonReader($this->appJsonFileName($locale)))->array();
    }
}
