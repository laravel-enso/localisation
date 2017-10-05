<?php

Route::middleware(['web', 'auth', 'core'])
    ->prefix('api/system')->as('system.')
    ->namespace('LaravelEnso\Localisation\app\Http\Controllers')
    ->group(function () {
        Route::prefix('localisation')->as('localisation.')
            ->group(function () {
                Route::get('initTable', 'LocalisationTableController@initTable')
                    ->name('initTable');
                Route::get('getTableData', 'LocalisationTableController@getTableData')
                    ->name('getTableData');
                Route::get('exportExcel', 'LocalisationTableController@exportExcel')
                    ->name('exportExcel');

                Route::get('editTexts', 'LangFileController@editTexts')
                    ->name('editTexts');
                Route::get('getLangFile/{language}', 'LangFileController@getLangFile')
                    ->name('getLangFile');
                Route::patch('saveLangFile/{language}', 'LangFileController@saveLangFile')
                    ->name('saveLangFile');
            });

        Route::resource('localisation', 'LocalisationController', ['except' => ['show']]);
    });
