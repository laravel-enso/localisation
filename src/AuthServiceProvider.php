<?php

namespace LaravelEnso\Localisation;

use LaravelEnso\Localisation\app\Models\Language;
use LaravelEnso\Localisation\app\Policies\LanguagePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies;

    public function boot()
    {
        $this->policies = [
            Language::class => LanguagePolicy::class,
        ];

        $this->registerPolicies();
    }
}
