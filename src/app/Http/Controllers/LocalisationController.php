<?php

namespace LaravelEnso\Localisation\app\Http\Controllers;

use Illuminate\Routing\Controller;
use LaravelEnso\Localisation\app\Classes\Storer;
use LaravelEnso\Localisation\app\Classes\Updater;
use LaravelEnso\Localisation\app\Models\Language;
use LaravelEnso\Localisation\app\Classes\Destroyer;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use LaravelEnso\Localisation\app\Forms\Builders\LocalisationForm;
use LaravelEnso\Localisation\app\Http\Requests\ValidateLanguageRequest;

class LocalisationController extends Controller
{
    use AuthorizesRequests;

    public function create(LocalisationForm $form)
    {
        return ['form' => $form->create()];
    }

    public function store(ValidateLanguageRequest $request)
    {
        $localisation = (new Storer($request->validated()))
            ->create();

        return [
            'message' => __('The language was successfully created'),
            'redirect' => 'system.localisation.edit',
            'param' => ['localisation' => $localisation->id],
        ];
    }

    public function edit(Language $localisation, LocalisationForm $form)
    {
        return ['form' => $form->edit($localisation)];
    }

    public function update(ValidateLanguageRequest $request, Language $localisation)
    {
        (new Updater($localisation, $request->validated()))
            ->run();

        return [
            'message' => __('The language was successfully updated'),
        ];
    }

    public function destroy(Language $localisation)
    {
        $this->authorize('destroy', $localisation);

        (new Destroyer($localisation))
            ->run();

        return [
            'message' => __('The language was successfully deleted'),
            'redirect' => 'system.localisation.index',
        ];
    }
}
