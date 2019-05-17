<?php

namespace LaravelEnso\Localisation\app\Http\Controllers\Language;

use Illuminate\Routing\Controller;
use LaravelEnso\Localisation\app\Models\Language;
use LaravelEnso\Localisation\app\Forms\Builders\LocalisationForm;

class Edit extends Controller
{
    public function __invoke(Language $localisation, LocalisationForm $form)
    {
        return ['form' => $form->edit($localisation)];
    }
}
