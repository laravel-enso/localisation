<?php

namespace LaravelEnso\Localisation;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use LaravelEnso\Localisation\Models\Language;
use LaravelEnso\Localisation\Policies\Language as Policy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Language::class => Policy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();
    }
}
