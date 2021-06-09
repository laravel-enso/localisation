<?php

namespace LaravelEnso\Localisation\Services;

use Illuminate\Support\Facades\DB;
use LaravelEnso\Localisation\Models\Language;
use LaravelEnso\Localisation\Services\Json\Storer as JsonStorer;
use LaravelEnso\Localisation\Services\Legacy\Storer as LegacyStorer;

class Storer
{
    public function __construct(
        private array $request,
        private ?string $flagSuffix
    ) {
    }

    public function create()
    {
        return DB::transaction(function () {
            $language = (new Language())
                ->storeWithFlagSufix($this->request, $this->flagSuffix);

            (new LegacyStorer($this->request['name']))->create();
            (new JsonStorer($this->request['name']))->create();

            return $language;
        });
    }
}
