<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['api', 'auth', 'core'])
    ->prefix('api/system/localisation')
    ->as('system.localisation.')
    ->group(function () {
        require __DIR__.'/app/json.php';
        require __DIR__.'/app/language.php';
    });
