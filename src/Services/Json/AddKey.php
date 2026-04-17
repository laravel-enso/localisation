<?php

namespace LaravelEnso\Localisation\Services\Json;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use LaravelEnso\Helpers\Services\JsonReader;
use LaravelEnso\Localisation\Models\Language;

class AddKey
{
    public function __construct(private array $keys, private ?array $locales = null)
    {
    }

    public function handle(): void
    {
        Collection::wrap($this->locales ?? Language::extra()->pluck('name')->all())
            ->each(fn (string $locale) => $this->addTo($locale));
    }

    private function addTo(string $locale): void
    {
        $path = App::langPath("{$locale}.json");

        $langFile = File::exists($path) ? (new JsonReader($path))->array() : [];

        $updated = Collection::wrap($this->keys)->merge($langFile)->all();

        if ($updated !== $langFile) {
            SaveToDisk::handle($locale, $updated);
        }
    }
}
