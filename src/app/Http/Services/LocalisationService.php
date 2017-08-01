<?php

namespace LaravelEnso\Localisation\app\Http\Services;

use Illuminate\Http\Request;
use LaravelEnso\Localisation\app\Classes\JsonLangManager;
use LaravelEnso\Localisation\app\Classes\LegacyLangManager;
use LaravelEnso\Localisation\app\Models\Language;

class LocalisationService
{
    const FlagClassPrefix = 'flag-icon flag-icon-';

    private $request;
    private $legacyLang;
    private $jsonLang;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->legacyLang = new LegacyLangManager();
        $this->jsonLang = new JsonLangManager();
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
            $localisation->fill($this->request->all());
            $localisation->flag = self::FlagClassPrefix.$localisation->name;
            $this->legacyLang->createLocale($localisation->name);
            $this->jsonLang->createEmptyLangFile($localisation->name);
            $localisation->save();
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
            $localisation->fill($this->request->all());
            $localisation->flag = self::FlagClassPrefix.$localisation->name;
            $localisation->save();
            $this->jsonLang->rename($oldName, $localisation->name);
            $this->legacyLang->renameFolder($oldName, $localisation->name);
        });

        flash()->success(__(config('labels.savedChanges')));

        return back();
    }

    public function destroy(Language $localisation)
    {
        \DB::transaction(function () use ($localisation) {
            $localisation->delete();
            $this->jsonLang->delete($localisation->name);
            $this->legacyLang->delete($localisation->name);
        });

        return ['message' => __(config('labels.successfulOperation'))];
    }
}
