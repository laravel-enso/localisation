<?php

namespace LaravelEnso\Localisation\Services;

use Illuminate\Support\Facades\DB;
use LaravelEnso\Localisation\Models\Language;
use LaravelEnso\Localisation\Services\Json\Storer as JsonStorer;
use LaravelEnso\Localisation\Services\Legacy\Storer as LegacyStorer;

class Storer
{
    private array $request;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function create()
    {
        return DB::transaction(function () {
            $language = (new Language())
                ->storeWithFlagSufix($this->request, $this->request['flag_sufix']);

            (new LegacyStorer($this->request['name']))->create();
            (new JsonStorer($this->request['name']))->create();

            return $language;
        });
    }
}
