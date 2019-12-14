<?php

Route::middleware(['web', 'auth', 'core'])
    ->namespace('LaravelEnso\Localisation\app\Http\Controllers')
    ->prefix('api/system/localisation')
    ->as('system.localisation.')
    ->group(function () {
        require 'app/jsons.php';
        require 'app/languages.php';
    });
