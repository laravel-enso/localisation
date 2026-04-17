<?php

namespace LaravelEnso\Localisation\Upgrades;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use LaravelEnso\Localisation\Models\Language;
use LaravelEnso\Upgrade\Contracts\MigratesData;

class UpdateFlags implements MigratesData
{
    private string $prefix = 'flag-icon flag-icon-';

    public function isMigrated(): bool
    {
        return $this->query()->doesntExist();
    }

    public function migrateData(): void
    {
        $this->query()->get()
            ->each(fn (Language $language) => $language
                ->update(['flag' => Str::of($language->flag)->replace($this->prefix, '')]));
    }

    private function query(): Builder
    {
        return Language::where('flag', 'LIKE', "{$this->prefix}%");
    }
}
