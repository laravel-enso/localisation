<?php

namespace LaravelEnso\Localisation\app\Http\Controllers\Language;

use Illuminate\Routing\Controller;
use LaravelEnso\Tables\app\Traits\Data;
use LaravelEnso\Localisation\app\Tables\Builders\LanguageTable;

class TableData extends Controller
{
    use Data;

    protected $tableClass = LanguageTable::class;
}
