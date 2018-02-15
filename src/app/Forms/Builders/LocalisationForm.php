<?php

namespace LaravelEnso\Localisation\app\Forms\Builders;

use LaravelEnso\FormBuilder\app\Classes\Form;
use LaravelEnso\Localisation\app\Models\Language;

class LocalisationForm
{
    private const FormPath = __DIR__.'/../Templates/localisation.json';

    private $form;

    public function __construct()
    {
        $this->form = new Form(self::FormPath);
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
