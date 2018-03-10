<?php

namespace LaravelEnso\Localisation\app\Http\Controllers;

use App\Http\Controllers\Controller;
use LaravelEnso\Localisation\app\Handlers\Storer;
use LaravelEnso\Localisation\app\Models\Language;
use LaravelEnso\Localisation\app\Handlers\Updater;
use LaravelEnso\Localisation\app\Handlers\Destroyer;
use LaravelEnso\Localisation\app\Forms\Builders\LocalisationForm;
use LaravelEnso\Localisation\app\Http\Requests\ValidateLanguageRequest;

class LocalisationController extends Controller
{
    public function create(LocalisationForm $form)
    {
        return ['form' => $form->create()];
    }

    public function store(ValidateLanguageRequest $request)
    {
        $localisation = (new Storer($request->all()))->create();

        return [
            'message' => __('The language was created!'),
            'redirect' => 'system.localisation.edit',
            'id' => $localisation->id,
        ];
    }

    public function edit(Language $localisation, LocalisationForm $form)
    {
        return ['form' => $form->edit($localisation)];
    }

    public function update(ValidateLanguageRequest $request, Language $localisation)
    {
        (new Updater($localisation, $request->all()))->run();

        return [
            'message' => __(config('enso.labels.savedChanges')),
        ];
    }

    public function destroy(Language $localisation)
    {
        $this->authorize('destroy', $localisation);

        (new Destroyer($localisation))->run();

        return [
            'message' => __(config('enso.labels.successfulOperation')),
            'redirect' => 'system.localisation.index',
        ];
    }
}
