<?php

namespace LaravelEnso\Localisation\App\Tables\Builders;

use Illuminate\Database\Eloquent\Builder;
use LaravelEnso\Localisation\App\Models\Language;
use LaravelEnso\Tables\App\Contracts\Table;

class LanguageTable implements Table
{
    protected const TemplatePath = __DIR__.'/../Templates/languages.json';

    public function query(): Builder
    {
        return Language::selectRaw('
            languages.id, languages.display_name, languages.name,
            languages.flag, is_rtl, is_active, languages.created_at
        ');
    }

    public function templatePath(): string
    {
        return static::TemplatePath;
    }
}
