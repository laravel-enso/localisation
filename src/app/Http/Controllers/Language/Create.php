<?php

namespace LaravelEnso\Localisation\app\Http\Controllers\Language;

use Illuminate\Routing\Controller;
use LaravelEnso\Localisation\app\Forms\Builders\LanguageForm;

class Create extends Controller
{
    public function __invoke(LanguageForm $form)
    {
        return ['form' => $form->create()];
    }
}
