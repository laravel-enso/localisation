<?php

use LaravelEnso\Migrator\App\Database\Migration;
use LaravelEnso\Permissions\App\Enums\Types;

class CreateStructureForLocalisation extends Migration
{
    protected $permissions = [
        ['name' => 'system.localisation.index', 'description' => 'Localisation index', 'type' => Types::Read, 'is_default' => false],
        ['name' => 'system.localisation.initTable', 'description' => 'Init table data for localisation', 'type' => Types::Read, 'is_default' => false],
        ['name' => 'system.localisation.tableData', 'description' => 'Get table data for localisation', 'type' => Types::Read, 'is_default' => false],
        ['name' => 'system.localisation.exportExcel', 'description' => 'Export excel for localisation', 'type' => Types::Read, 'is_default' => false],
        ['name' => 'system.localisation.create', 'description' => 'Create langugage', 'type' => Types::Write, 'is_default' => false],
        ['name' => 'system.localisation.edit', 'description' => 'Edit language', 'type' => Types::Write, 'is_default' => false],
        ['name' => 'system.localisation.editTexts', 'description' => 'Edit lang file', 'type' => Types::Write, 'is_default' => false],
        ['name' => 'system.localisation.addKey', 'description' => 'Add new lang key', 'type' => Types::Write, 'is_default' => false],
        ['name' => 'system.localisation.merge', 'description' => 'Merge one or all the json lang files', 'type' => Types::Write, 'is_default' => false],
        ['name' => 'system.localisation.getLangFile', 'description' => 'Get selected lang file content', 'type' => Types::Read, 'is_default' => false],
        ['name' => 'system.localisation.saveLangFile', 'description' => 'Save edited lang file', 'type' => Types::Write, 'is_default' => false],
        ['name' => 'system.localisation.store', 'description' => 'Save newly created language', 'type' => Types::Write, 'is_default' => false],
        ['name' => 'system.localisation.update', 'description' => 'Save edited language', 'type' => Types::Write, 'is_default' => false],
        ['name' => 'system.localisation.destroy', 'description' => 'Delete language', 'type' => Types::Write, 'is_default' => false],
    ];

    protected $menu = [
        'name' => 'Localisation', 'icon' => 'language', 'route' => 'system.localisation.index', 'order_index' => 999, 'has_children' => false,
    ];

    protected $parentMenu = 'System';
}
