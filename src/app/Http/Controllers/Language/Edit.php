<?php

namespace LaravelEnso\Localisation\app\Http\Controllers\Language;

use Illuminate\Routing\Controller;
use LaravelEnso\Localisation\app\Forms\Builders\LanguageForm;
use LaravelEnso\Localisation\app\Models\Language;

class Edit extends Controller
{
    public function __invoke(Language $language, LanguageForm $form)
    {
        return ['form' => $form->edit($language)];
    }
}
