<?php

namespace LaravelEnso\Localisation\App\Http\Controllers\Language;

use Illuminate\Routing\Controller;
use LaravelEnso\Localisation\App\Tables\Builders\LanguageTable;
use LaravelEnso\Tables\App\Traits\Excel;

class ExportExcel extends Controller
{
    use Excel;

    protected $tableClass = LanguageTable::class;
}
