<?php

namespace LaravelEnso\Localisation\Forms\Builders;

use LaravelEnso\Forms\Services\Form;
use LaravelEnso\Localisation\Models\Language as Model;

class Language
{
    private const TemplatePath = __DIR__.'/../Templates/language.json';

    protected Form $form;

    public function __construct()
    {
        $this->form = new Form($this->templatePath());
    }

    public function create()
    {
        return $this->form->hide('flagIcon')->create();
    }

    public function edit(Model $language)
    {
        return $this->form
            ->value('flagIcon', $language->flag())
            ->edit($language);
    }

    protected function templatePath(): string
    {
        return self::TemplatePath;
    }
}
