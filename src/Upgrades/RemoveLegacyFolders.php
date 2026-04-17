<?php

namespace LaravelEnso\Localisation\Upgrades;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use LaravelEnso\Upgrade\Contracts\MigratesData;

class RemoveLegacyFolders implements MigratesData
{
    public function isMigrated(): bool
    {
        return Collection::wrap($this->folders())
            ->every(fn (string $folder) => ! File::exists($folder));
    }

    public function migrateData(): void
    {
        Collection::wrap($this->folders())
            ->filter(fn (string $folder) => File::exists($folder))
            ->each(fn (string $folder) => File::deleteDirectory($folder));
    }

    private function folders(): array
    {
        return [
            App::langPath('app'),
            App::langPath('enso'),
        ];
    }
}
