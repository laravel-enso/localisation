<?php

namespace LaravelEnso\Localisation;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use LaravelEnso\Localisation\app\Models\Language;
use LaravelEnso\Localisation\app\Policies\LanguagePolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Language::class => LanguagePolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();
    }
}
