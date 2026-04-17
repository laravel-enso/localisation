<?php

namespace LaravelEnso\Localisation\Observers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use LaravelEnso\Localisation\Models\Language as Model;
use LaravelEnso\Localisation\Services\Json\SaveToDisk;
use LaravelEnso\Localisation\Services\Json\Template;
use LaravelEnso\Localisation\Services\Legacy\Store;

class Language
{
    public function created(Model $language)
    {
        (new Store($language->name))->handle();
        SaveToDisk::handle($language->name, Template::handle($language->name));
    }

    public function updated(Model $language)
    {
        $old = $language->getOriginal('name');
        $new = $language->name;

        if ($old !== $new) {
            $path = fn ($locale) => App::langPath("{$locale}");

            File::move($path("{$old}.json"), $path("{$new}.json"));
            File::moveDirectory($path($old), $path($new));
        }
    }

    public function deleted(Model $language)
    {
        File::deleteDirectory(App::langPath($language->name));
        File::delete(App::langPath("{$language->name}.json"));
    }
}
