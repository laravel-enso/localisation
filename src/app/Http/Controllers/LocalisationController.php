<?php

namespace LaravelEnso\Localisation\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use LaravelEnso\Localisation\app\Http\Requests\ValidateLanguageRequest;
use LaravelEnso\Localisation\app\Http\Services\LocalisationService;
use LaravelEnso\Localisation\app\Models\Language;

class LocalisationController extends Controller
{
    private $localisation;

    public function __construct(Request $request)
    {
        $this->localisation = new LocalisationService($request);
    }

    public function index()
    {
        return view('laravel-enso/localisation::index');
    }

    public function create()
    {
        return $this->localisation->create();
    }

    public function store(ValidateLanguageRequest $request, Language $localisation)
    {
        return $this->localisation->store($localisation);
    }

    public function edit(Language $localisation)
    {
        return $this->localisation->edit($localisation);
    }

    public function update(ValidateLanguageRequest $request, Language $localisation)
    {
        return $this->localisation->update($localisation);
    }

    public function editTexts()
    {
        return $this->localisation->editTexts();
    }

    public function destroy(Language $localisation)
    {
        $this->authorize('destroy', $localisation);

        return $this->localisation->destroy($localisation);
    }
}
