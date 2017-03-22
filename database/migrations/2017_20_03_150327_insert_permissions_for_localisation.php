<?php

use Illuminate\Database\Migrations\Migration;
use LaravelEnso\Core\Models\Permission;
use LaravelEnso\Core\Models\PermissionsGroup;
use LaravelEnso\Core\Models\Role;

class InsertPermissionsForLocalisation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $permissionsGroup = PermissionsGroup::whereName('system.localisation')->first();

        if ($permissionsGroup) {
            return;
        }

        \DB::transaction(function () {
            $permissionsGroup = new PermissionsGroup([
                'name'        => 'system.localisation',
                'description' => 'Localisation Permissions Group',
            ]);

            $permissionsGroup->save();

            $permissions = [
                [
                    'name'        => 'system.localisation.initTable',
                    'description' => 'Init table data for localisation',
                    'type'        => 0,
                ],
                [
                    'name'        => 'system.localisation.getTableData',
                    'description' => 'Get table data for localisation',
                    'type'        => 0,
                ],
                [
                    'name'        => 'system.localisation.create',
                    'description' => 'Create Langugage',
                    'type'        => 1,
                ],
                [
                    'name'        => 'system.localisation.edit',
                    'description' => 'Edit Language',
                    'type'        => 1,
                ],
                [
                    'name'        => 'system.localisation.editTexts',
                    'description' => 'Edit Language File',
                    'type'        => 1,
                ],
                [
                    'name'        => 'system.localisation.getLangFile',
                    'description' => 'Get Selected Lang File Content',
                    'type'        => 0,
                ],
                [
                    'name'        => 'system.localisation.index',
                    'description' => 'Localisation Index',
                    'type'        => 0,
                ],
                [
                    'name'        => 'system.localisation.saveLangFile',
                    'description' => 'Save Lang File',
                    'type'        => 1,
                ],
                [
                    'name'        => 'system.localisation.store',
                    'description' => 'Save Language',
                    'type'        => 1,
                ],
                [
                    'name'        => 'system.localisation.update',
                    'description' => 'Save edited language',
                    'type'        => 1,
                ],
                [
                    'name'        => 'system.localisation.destroy',
                    'description' => 'Delete Language',
                    'type'        => 1,
                ],
            ];

            $adminRole = Role::whereName('admin')->first();

            foreach ($permissions as $permission) {
                $permission = new Permission($permission);
                $permissionsGroup->permissions()->save($permission);
                $adminRole->permissions()->save($permission);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::transaction(function () {
            $permissionsGroup = PermissionsGroup::whereName('system.localisation')->first();
            $permissionsGroup->permissions->each->delete();
            $permissionsGroup->delete();
        });
    }
}
