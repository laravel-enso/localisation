<?php

namespace LaravelEnso\Localisation\Http\Controllers\Language;

use Illuminate\Routing\Controller;
use LaravelEnso\Localisation\Forms\Builders\LanguageForm;

class Create extends Controller
{
    public function __invoke(LanguageForm $form)
    {
        return ['form' => $form->create()];
    }
}
