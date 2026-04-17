<?php

namespace LaravelEnso\Localisation\Upgrades;

use Illuminate\Database\Eloquent\Builder;
use LaravelEnso\Permissions\Models\Permission;
use LaravelEnso\Upgrade\Contracts\MigratesData;

class RemoveMergePermission implements MigratesData
{
    public function isMigrated(): bool
    {
        return $this->permission()->doesntExist();
    }

    public function migrateData(): void
    {
        $this->permission()->delete();
    }

    private function permission(): Builder
    {
        return Permission::whereName('system.localisation.merge');
    }
}
