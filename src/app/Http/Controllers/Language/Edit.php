<?php

namespace LaravelEnso\Localisation\App\Http\Controllers\Language;

use Illuminate\Routing\Controller;
use LaravelEnso\Localisation\App\Forms\Builders\LanguageForm;
use LaravelEnso\Localisation\App\Models\Language;

class Edit extends Controller
{
    public function __invoke(Language $language, LanguageForm $form)
    {
        return ['form' => $form->edit($language)];
    }
}
