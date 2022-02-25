<?php

namespace LaravelEnso\Localisation\Http\Controllers\Language;

use Illuminate\Routing\Controller;
use LaravelEnso\Localisation\Tables\Builders\Language;
use LaravelEnso\Tables\Traits\Init;

class InitTable extends Controller
{
    use Init;

    protected $tableClass = Language::class;
}
