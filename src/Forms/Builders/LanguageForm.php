<?php

namespace LaravelEnso\Localisation\Forms\Builders;

use LaravelEnso\Forms\Services\Form;
use LaravelEnso\Localisation\Models\Language;

class LanguageForm
{
    protected const FormPath = __DIR__.'/../Templates/language.json';

    protected Form $form;

    public function __construct()
    {
        $this->form = new Form(static::FormPath);
    }

    public function create()
    {
        return $this->form->hide('flag')
            ->create();
    }

    public function edit(Language $language)
    {
        return $this->form
            ->value('flag_sufix', substr($language->flag, -2))
            ->edit($language);
    }
}
