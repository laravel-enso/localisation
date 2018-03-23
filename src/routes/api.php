<?php

Route::middleware(['web', 'auth', 'core'])
    ->prefix('api/system')->as('system.')
    ->namespace('LaravelEnso\Localisation\app\Http\Controllers')
    ->group(function () {
        Route::prefix('localisation')->as('localisation.')
            ->group(function () {
                Route::get('initTable', 'LocalisationTableController@init')
                    ->name('initTable');
                Route::get('getTableData', 'LocalisationTableController@data')
                    ->name('getTableData');
                Route::get('exportExcel', 'LocalisationTableController@excel')
                    ->name('exportExcel');

                Route::get('editTexts', 'JsonFileController@index')
                    ->name('editTexts');
                Route::get('getLangFile/{subDir}/{language}', 'JsonFileController@edit')
                    ->name('getLangFile');
                Route::patch('saveLangFile/{subDir}/{language}', 'JsonFileController@update')
                    ->name('saveLangFile');
                Route::patch('addKey', 'JsonFileController@addKey')
                    ->name('addKey');
            });

        Route::resource('localisation', 'LocalisationController', ['except' => ['show']]);
    });
