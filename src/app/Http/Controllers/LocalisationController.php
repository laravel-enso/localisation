<?php

namespace LaravelEnso\Localisation\app\Http\Controllers;

use App\Http\Controllers\Controller;
use LaravelEnso\Localisation\app\Models\Language;
use LaravelEnso\Localisation\app\Http\Services\LocalisationService;
use LaravelEnso\Localisation\app\Http\Requests\ValidateLanguageRequest;

class LocalisationController extends Controller
{
    private $service;

    public function __construct(LocalisationService $service)
    {
        $this->service = $service;
    }

    public function create()
    {
        return $this->service->create();
    }

    public function store(ValidateLanguageRequest $request, Language $localisation)
    {
        return $this->service->store($request, $localisation);
    }

    public function edit(Language $localisation)
    {
        return $this->service->edit($localisation);
    }

    public function update(ValidateLanguageRequest $request, Language $localisation)
    {
        return $this->service->update($request, $localisation);
    }

    public function destroy(Language $localisation)
    {
        $this->authorize('destroy', $localisation);

        return $this->service->destroy($localisation);
    }
}
