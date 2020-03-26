<?php

namespace LaravelEnso\Localisation\App\Http\Controllers\Language;

use Illuminate\Routing\Controller;
use LaravelEnso\Localisation\App\Tables\Builders\LanguageTable;
use LaravelEnso\Tables\App\Traits\Init;

class InitTable extends Controller
{
    use Init;

    protected $tableClass = LanguageTable::class;
}
