<?php

namespace LaravelEnso\Localisation\app\Tables\Builders;

use LaravelEnso\VueDatatable\app\Classes\Table;
use LaravelEnso\Localisation\app\Models\Language;

class LocalisationTable extends Table
{
    protected $templatePath = __DIR__.'/../Templates/localisation.json';

    public function query()
    {
        return Language::select(\DB::raw('
            languages.id as "dtRowId", languages.display_name, languages.name,
            languages.flag, is_active, languages.created_at
        '));
    }
}
