<?php

namespace LaravelEnso\Localisation\Http\Controllers\Language;

use Illuminate\Routing\Controller;
use LaravelEnso\Localisation\Forms\Builders\LanguageForm;
use LaravelEnso\Localisation\Models\Language;

class Edit extends Controller
{
    public function __invoke(Language $language, LanguageForm $form)
    {
        return ['form' => $form->edit($language)];
    }
}
