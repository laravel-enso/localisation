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

    const FormPath = __DIR__ . '/../../Forms/localisation.json';

    private $legacyLang;
    private $jsonLang;

    public function __construct()
    {
        $this->legacyLang = new LegacyLangManager();
        $this->jsonLang   = new JsonLangManager();
    }

    public function create()
    {
        $form = (new FormBuilder(self::FormPath))
            ->setMethod('POST')
            ->setTitle('Create Language')
            ->getData();

        return compact('form');
    }

    public function store(Request $request, Language $localisation)
    {
        \DB::transaction(function () use ($request, &$localisation) {
            $localisation->fill($request->all());
            $localisation->flag = self::FlagClassPrefix . $localisation->name;
            $this->legacyLang->createLocale($localisation->name);
            $this->jsonLang->createEmptyLangFile($localisation->name);
            $localisation->save();
        });

        return [
            'message'  => __('The language was created!'),
            'redirect' => 'system.localisation.edit',
            'id'       => $localisation->id,
        ];
    }

    public function edit(Language $localisation)
    {
        $form = (new FormBuilder(self::FormPath, $localisation))
            ->setMethod('PATCH')
            ->setTitle('Edit Language')
            ->setValue('flag_sufix', substr($localisation->flag, -2))
            ->getData();

        return compact('form');
    }

    public function update(Request $request, Language $localisation)
    {
        \DB::transaction(function () use ($request, $localisation) {
            $oldName = $localisation->name;
            $localisation->fill($request->all());
            $localisation->flag = self::FlagClassPrefix . $request->get('flag_sufix');
            $localisation->save();
            $this->jsonLang->rename($oldName, $localisation->name);
            $this->legacyLang->renameFolder($oldName, $localisation->name);
        });

        return [
            'message' => __(config('enso.labels.savedChanges')),
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
            'message'  => __(config('enso.labels.successfulOperation')),
            'redirect' => 'administration.localisation.index',
        ];
    }
}
