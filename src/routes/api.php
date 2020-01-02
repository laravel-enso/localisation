<?php

Route::middleware(['web', 'auth', 'core'])
    ->namespace('LaravelEnso\Localisation\App\Http\Controllers')
    ->prefix('api/system/localisation')
    ->as('system.localisation.')
    ->group(function () {
        require 'app/json.php';
        require 'app/language.php';
    });
