<?php

namespace LaravelEnso\Products\Upgrades;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use LaravelEnso\Upgrade\Contracts\MigratesData;

class MoveLangFiles implements MigratesData
{
    public function isMigrated(): bool
    {
        return File::exists(App::langPath());
    }

    public function migrateData(): void
    {
        File::moveDirectory(resource_path('lang'), App::langPath());
    }
}
