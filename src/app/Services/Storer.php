<?php

namespace LaravelEnso\Localisation\app\Services;

use Illuminate\Support\Facades\DB;
use LaravelEnso\Localisation\app\Models\Language;
use LaravelEnso\Localisation\app\Services\Json\Storer as JsonStorer;
use LaravelEnso\Localisation\app\Services\Legacy\Storer as LegacyStorer;

class Storer
{
    private $request;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function create()
    {
        $language = null;

        DB::transaction(function () use (&$language) {
            $language = new Language();
            $language = $language
                ->storeWithFlagSufix($this->request, $this->request['flag_sufix']);

            (new LegacyStorer($this->request['name']))->create();
            (new JsonStorer($this->request['name']))->create();
            $language->save();
        });

        return $language;
    }
}
