<?php

namespace LaravelEnso\Localisation\app\Forms\Builders;

use LaravelEnso\Forms\app\Services\Form;
use LaravelEnso\Localisation\app\Models\Language;

class LocalisationForm
{
    protected const FormPath = __DIR__.'/../Templates/localisation.json';

    protected $form;

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
