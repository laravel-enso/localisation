<?php

namespace LaravelEnso\Localisation\App\Http\Controllers\Language;

use Illuminate\Routing\Controller;
use LaravelEnso\Localisation\App\Tables\Builders\LanguageTable;
use LaravelEnso\Tables\App\Traits\Data;

class TableData extends Controller
{
    use Data;

    protected $tableClass = LanguageTable::class;
}
