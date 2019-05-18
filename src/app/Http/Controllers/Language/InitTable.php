<?php

namespace LaravelEnso\Localisation\app\Http\Controllers\Language;

use Illuminate\Routing\Controller;
use LaravelEnso\Tables\app\Traits\Init;
use LaravelEnso\Localisation\app\Tables\Builders\LocalisationTable;

class InitTable extends Controller
{
    use Init;

    protected $tableClass = LocalisationTable::class;
}
