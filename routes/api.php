<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['api', 'auth', 'core'])
    ->namespace('LaravelEnso\Localisation\Http\Controllers')
    ->prefix('api/system/localisation')
    ->as('system.localisation.')
    ->group(function () {
        require 'app/json.php';
        require 'app/language.php';
    });
