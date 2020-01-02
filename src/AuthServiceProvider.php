<?php

namespace LaravelEnso\Localisation;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use LaravelEnso\Localisation\App\Models\Language;
use LaravelEnso\Localisation\App\Policies\Language as Policy;

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
