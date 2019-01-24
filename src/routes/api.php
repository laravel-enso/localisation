<?php

Route::middleware(['web', 'auth', 'core'])
    ->prefix('api/system')->as('system.')
    ->namespace('LaravelEnso\Localisation\app\Http\Controllers')
    ->group(function () {
        Route::prefix('localisation')->as('localisation.')
            ->group(function () {
                Route::get('initTable', 'LocalisationTableController@init')
                    ->name('initTable');
                Route::get('tableData', 'LocalisationTableController@data')
                    ->name('tableData');
                Route::get('exportExcel', 'LocalisationTableController@excel')
                    ->name('exportExcel');

                Route::get('editTexts', 'JsonFileController@index')
                    ->name('editTexts');
                Route::get('getLangFile/{language}/{subDir}', 'JsonFileController@edit')
                    ->name('getLangFile');
                Route::patch('saveLangFile/{language}/{subDir}', 'JsonFileController@update')
                    ->name('saveLangFile');
                Route::patch('addKey', 'JsonFileController@addKey')
                    ->name('addKey');
                Route::patch('merge/{locale?}', 'JsonFileController@merge')
                    ->name('merge');
            });

        Route::resource('localisation', 'LocalisationController', ['except' => ['show', 'index']]);
    });
