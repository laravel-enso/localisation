<?php

namespace LaravelEnso\Localisation\app\Http\Controllers;

use App\Http\Controllers\Controller;
use LaravelEnso\Localisation\app\Models\Language;
use LaravelEnso\Localisation\app\Http\Services\LocalisationService;
use LaravelEnso\Localisation\app\Http\Requests\ValidateLanguageRequest;

class LocalisationController extends Controller
{
    public function create(LocalisationService $service)
    {
        return $service->create();
    }

    public function store(ValidateLanguageRequest $request, Language $localisation, LocalisationService $service)
    {
        return $service->store($request, $localisation);
    }

    public function edit(Language $localisation, LocalisationService $service)
    {
        return $service->edit($localisation);
    }

    public function update(ValidateLanguageRequest $request, Language $localisation, LocalisationService $service)
    {
        return $service->update($request, $localisation);
    }

    public function destroy(Language $localisation, LocalisationService $service)
    {
        $this->authorize('destroy', $localisation);

        return $service->destroy($localisation);
    }
}
