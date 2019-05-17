<?php

namespace LaravelEnso\Localisation\app\Http\Controllers\Language;

use Illuminate\Routing\Controller;
use LaravelEnso\Tables\app\Traits\Excel;
use LaravelEnso\Tables\app\Traits\Datatable;
use LaravelEnso\Localisation\app\Tables\Builders\LocalisationTable;

class Table extends Controller
{
    use Datatable, Excel;

    protected $tableClass = LocalisationTable::class;
}
