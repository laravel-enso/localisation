<?php

use Illuminate\Database\Migrations\Migration;
use LaravelEnso\Core\App\Classes\StructureManager\StructureSupport;

class CreateStructureForLocalisation extends Migration
{
    use StructureSupport;

    private $permissionsGroup = [
        'name' => 'system.localisation', 'description' => 'Localisation Permissions Group',
    ];

    private $permissions = [
        ['name' => 'system.localisation.initTable', 'description' => 'Init table data for localisation', 'type' => 0],
        ['name' => 'system.localisation.getTableData', 'description' => 'Get table data for localisation', 'type' => 0],
        ['name' => 'system.localisation.create', 'description' => 'Create Langugage', 'type' => 1],
        ['name' => 'system.localisation.edit', 'description' => 'Edit Language', 'type' => 1],
        ['name' => 'system.localisation.editTexts', 'description' => 'Edit Language File', 'type' => 1],
        ['name' => 'system.localisation.getLangFile', 'description' => 'Get Selected Lang File Content', 'type' => 0],
        ['name' => 'system.localisation.index', 'description' => 'Localisation Index', 'type' => 0],
        ['name' => 'system.localisation.saveLangFile', 'description' => 'Save Lang File', 'type' => 1],
        ['name' => 'system.localisation.store', 'description' => 'Save Language', 'type' => 1],
        ['name' => 'system.localisation.update', 'description' => 'Save edited language', 'type' => 1],
        ['name' => 'system.localisation.destroy', 'description' => 'Delete Language', 'type' => 1],
    ];

    private $menu = [
        'name' => 'Localisation', 'icon' => 'fa fa-fw fa-language', 'link' => 'system/localisation', 'has_children' => 0,
    ];

    private $parentMenu = 'System';
    private $roles;
}