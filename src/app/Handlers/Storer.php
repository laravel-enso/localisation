<?php

namespace LaravelEnso\Localisation\app\Handlers;

use LaravelEnso\Localisation\app\Models\Language;
use LaravelEnso\Localisation\app\Handlers\Json\Storer as JsonStorer;
use LaravelEnso\Localisation\app\Handlers\Legacy\Storer as LegacyStorer;

class Storer
{
    private $request;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function create()
    {
        $localisation = null;

        \DB::transaction(function () use (&$localisation) {
            $localisation = new Language();
            $localisation = $localisation
                ->storeWithFlagSufix($this->request, $this->request['flag_sufix']);

            (new LegacyStorer($this->request['name']))->create();
            (new JsonStorer($this->request['name']))->create();
            $localisation->save();
        });

        return $localisation;
    }
}
