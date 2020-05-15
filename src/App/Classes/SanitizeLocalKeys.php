<?php

namespace LaravelEnso\Localisation\App\Classes;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\File;
use LaravelEnso\Localisation\App\Services\Traits\JsonFilePathResolver;

class SanitizeLocalKeys
{
    use JsonFilePathResolver;

    private Collection $app;
    private Collection $core;

    public function __construct(array $app, array $core)
    {
        $this->app = new Collection($app);
        $this->core = new Collection($core);
    }

    public function sanitize(string $locale): array
    {
        $sanitized = $this->app->reject(fn ($value, $key) => $this->core
            ->keys()->contains($key))->toArray();

        File::put(
            $this->appJsonFileName($locale),
            json_encode($sanitized, JSON_FORCE_OBJECT)
        );

        return $sanitized;
    }
}
