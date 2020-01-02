<?php

namespace LaravelEnso\Localisation\App\Services;

use Illuminate\Support\Facades\DB;
use LaravelEnso\Localisation\App\Models\Language;
use LaravelEnso\Localisation\App\Services\Json\Storer as JsonStorer;
use LaravelEnso\Localisation\App\Services\Legacy\Storer as LegacyStorer;

class Storer
{
    private array $request;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function create()
    {
        DB::beginTransaction();

        $language = (new Language())
                ->storeWithFlagSufix($this->request, $this->request['flag_sufix']);

        (new LegacyStorer($this->request['name']))->create();
        (new JsonStorer($this->request['name']))->create();

        DB::commit();

        return $language;
    }
}
