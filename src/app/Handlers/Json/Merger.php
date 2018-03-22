<?php

namespace LaravelEnso\Localisation\app\Handlers\Json;

use LaravelEnso\Localisation\app\Models\Language;

class Merger extends Handler
{
    const ALL = "ALL";

    public function run(string $inputLocale = self::ALL)
    {
        $lang = Language::extra();
        if ($inputLocale !== self::ALL) {
            $lang->where('name', $inputLocale);
        }
          
        $lang->pluck('name')
            ->each(function ($locale) {
                $this->merge($locale);
            });
    }

   
    protected function merge(string $locale)
    {
        $core = (array) $this->jsonFileContent($this->jsonFileNameCore($locale));
        $app = (array) $this->jsonFileContent($this->jsonFileNameApp($locale));

        $this->saveToDisk($locale, array_merge($core, $app), "");
    }

}
