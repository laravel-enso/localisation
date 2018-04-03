<?php

namespace LaravelEnso\Localisation\app\Http\Controllers;

use App\Http\Controllers\Controller;
use LaravelEnso\VueDatatable\app\Traits\Excel;
use LaravelEnso\VueDatatable\app\Traits\Datatable;
use LaravelEnso\Localisation\app\Tables\Builders\LocalisationTable;

class LocalisationTableController extends Controller
{
    use Datatable, Excel;

    protected $tableClass = LocalisationTable::class;
}
