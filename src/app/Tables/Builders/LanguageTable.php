<?php

namespace LaravelEnso\Localisation\app\Tables\Builders;

use LaravelEnso\Tables\app\Services\Table;
use LaravelEnso\Localisation\app\Models\Language;

class LanguageTable extends Table
{
    protected $templatePath = __DIR__.'/../Templates/languages.json';

    public function query()
    {
        return Language::selectRaw('
            languages.id, languages.display_name, languages.name,
            languages.flag, is_rtl, is_active, languages.created_at
        ');
    }
}
