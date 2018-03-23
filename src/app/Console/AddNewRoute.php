<?php

namespace LaravelEnso\Localisation\app\Console;

use Illuminate\Console\Command;
use LaravelEnso\RoleManager\app\Models\Role;
use LaravelEnso\PermissionManager\app\Models\Permission;
use LaravelEnso\PermissionManager\app\Models\PermissionGroup;

class AddNewRoute extends Command
{
    protected $signature = 'localisation:add-route';
    protected $description = 'Adds the new route for automatic collection of the missing keys';

    public function handle()
    {
        if (Permission::whereName('system.localisation.addKey')->count()) {
            $this->info('Route was previously added');

            return;
        }

        \DB::transaction(function () {
            $groupId = PermissionGroup::whereName('system.localisation')
                ->first()->id;

            $permission = Permission::create([
                'permission_group_id' => $groupId,
                'name' => 'system.localisation.addKey',
                'description' => 'Add new lang key',
                'type' => 1,
                'default' => false,
            ]);

            $permission->roles()->sync(Role::whereName('admin')->first()->id);
        });

        $this->info('Route was added');
    }
}
