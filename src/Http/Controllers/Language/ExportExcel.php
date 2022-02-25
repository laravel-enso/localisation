<?php

namespace LaravelEnso\Localisation\Http\Controllers\Language;

use Illuminate\Routing\Controller;
use LaravelEnso\Localisation\Tables\Builders\Language;
use LaravelEnso\Tables\Traits\Excel;

class ExportExcel extends Controller
{
    use Excel;

    protected $tableClass = Language::class;
}
