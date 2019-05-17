<?php

namespace LaravelEnso\Localisation\app\Http\Controllers\Language;

use Illuminate\Routing\Controller;
use LaravelEnso\Localisation\app\Forms\Builders\LocalisationForm;

class Create extends Controller
{
    public function __invoke(LocalisationForm $form)
    {
        return ['form' => $form->create()];
    }
}
