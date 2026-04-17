<?php

namespace LaravelEnso\Localisation;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use LaravelEnso\Localisation\Models\Language;
use LaravelEnso\Localisation\Observers\Language as Observer;

class EventServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Language::observe(Observer::class);
    }
}
