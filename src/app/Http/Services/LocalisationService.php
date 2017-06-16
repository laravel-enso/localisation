<?php

namespace LaravelEnso\Localisation\app\Http\Services;

use Illuminate\Http\Request;
use LaravelEnso\Core\app\Models\Language;
use LaravelEnso\Localisation\app\Classes\JsonLangManager;
use LaravelEnso\Localisation\app\Classes\LegacyLangManager;

class LocalisationService
{
    private $request;
    private $jsonLang;
    private $legacyLang;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->jsonLang = new JsonLangManager();
        $this->legacyLang = new LegacyLangManager();
    }

    public function getTableQuery()
    {
        return Language::select(\DB::raw('languages.id as DT_RowId, languages.display_name,
            languages.name, languages.flag, languages.created_at, languages.updated_at'));
    }

    public function index()
    {
        return view('laravel-enso/localisation::index');
    }

    public function create(Language $localisation)
    {
        return view('laravel-enso/localisation::create', compact('localisation'));
    }

    public function store(Language $localisation)
    {
        \DB::transaction(function () use (&$localisation) {
            $localisation = $localisation->create($this->request->all());
            $this->jsonLang->createEmptyLangFile($localisation->name);
            $this->legacyLang->createLocale($localisation->name);
        });

        flash()->success(__('Language Created'));

        return redirect('system/localisation/'.$localisation->id.'/edit');
    }

    public function edit(Language $localisation)
    {
        return view('laravel-enso/localisation::edit', compact('localisation'));
    }

    public function update(Language $localisation)
    {
        \DB::transaction(function () use ($localisation) {
            $oldName = $localisation->name;
            $localisation->update($this->request->all());
            $this->jsonLang->rename($oldName, $localisation->name);
            $this->legacyLang->renameFolder($oldName, $localisation->name);
        });

        flash()->success(__('The Changes have been saved!'));

        return back();
    }

    public function destroy(Language $localisation)
    {
        \DB::transaction(function () use ($localisation) {
            $localisation->delete();
            $this->jsonLang->delete($localisation->name);
            $this->legacyLang->delete($localisation->name);
        });

        return ['message' => __('Operation was successful')];
    }
}
