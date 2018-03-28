<?php

namespace LaravelEnso\Localisation\app\Console;

use Illuminate\Console\Command;
use LaravelEnso\RoleManager\app\Models\Role;
use LaravelEnso\PermissionManager\app\Models\Permission;
use LaravelEnso\PermissionManager\app\Models\PermissionGroup;

class AddRoutes extends Command
{
    protected $signature = 'localisation:add-routes';
    protected $description = 'Adds the new routes for the automatic collection of missing keys and the merge for all json lang files';

    public function handle()
    {
        $routes = collect([
            'system.localisation.addKey',
            'system.localisation.merge',
        ]);

        if (Permission::whereName($routes->first())->count()) {
            $this->info('Route '.$routes->first().' was previously added');
            $routes->shift();
        }

        if (Permission::whereName($routes->last())->count()) {
            $this->info('Route '.$routes->last().' was previously added');
            $routes->shift();
        }

        if ($routes->isEmpty()) {
            return;
        }

        \DB::transaction(function () use ($routes) {
            $groupId = PermissionGroup::whereName('system.localisation')
                ->first()->id;

            $routes->each(function ($route) use ($groupId) {
                $permission = Permission::create([
                    'permission_group_id' => $groupId,
                    'name' => $route,
                    'description' => 'Add new lang key',
                    'type' => 1,
                    'default' => false,
                ]);

                $permission->roles()->sync(Role::whereName('admin')->first()->id);
            });
        });

        $this->info('Routes were updated');
    }
}
