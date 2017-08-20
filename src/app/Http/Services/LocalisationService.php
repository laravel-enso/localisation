<?php

namespace LaravelEnso\Localisation\app\Http\Services;

use Illuminate\Http\Request;
use LaravelEnso\FormBuilder\app\Classes\FormBuilder;
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

    public function create()
    {
        $form = (new FormBuilder(__DIR__.'/../../Forms/localisation.json'))
            ->setAction('POST')
            ->setTitle('Create Language')
            ->setUrl('/system/localisation')
            ->getData();

        return view('laravel-enso/localisation::create', compact('form'));
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

        return [
            'message'  => __('The language was created!'),
            'redirect' => '/system/localisation/'.$localisation->id.'/edit',
        ];
    }

    public function edit(Language $localisation)
    {
        $form = (new FormBuilder(__DIR__.'/../../Forms/localisation.json', $localisation))
            ->setAction('PATCH')
            ->setTitle('Edit Language')
            ->setValue('flag_sufix', substr($localisation->flag, -2))
            ->setUrl('/system/localisation/'.$localisation->id)
            ->getData();

        return view('laravel-enso/localisation::edit', compact('form'));
    }

    public function update(Language $localisation)
    {
        \DB::transaction(function () use ($localisation) {
            $oldName = $localisation->name;
            $localisation->fill($this->request->all());
            $localisation->flag = self::FlagClassPrefix.$this->request->get('flag_sufix');
            $localisation->save();
            $this->jsonLang->rename($oldName, $localisation->name);
            $this->legacyLang->renameFolder($oldName, $localisation->name);
        });

        return [
            'message' => __(config('labels.savedChanges')),
        ];
    }

    public function destroy(Language $localisation)
    {
        \DB::transaction(function () use ($localisation) {
            $localisation->delete();
            $this->jsonLang->delete($localisation->name);
            $this->legacyLang->delete($localisation->name);
        });

        return [
            'message'  => __(config('labels.successfulOperation')),
            'redirect' => '/system/localisation/',
        ];
    }
}
