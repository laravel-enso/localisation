<?php

namespace LaravelEnso\Localisation\app\Http\Controllers\Language;

use Illuminate\Routing\Controller;
use LaravelEnso\Tables\app\Traits\Excel;
use LaravelEnso\Localisation\app\Tables\Builders\LocalisationTable;

class ExportExcel extends Controller
{
    use Excel;

    protected $tableClass = LocalisationTable::class;
}
