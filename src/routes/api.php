<?php

Route::middleware(['web', 'auth', 'core'])
    ->namespace('LaravelEnso\Localisation\app\Http\Controllers')
    ->prefix('api/system/localisation')
    ->as('system.localisation.')
    ->group(function () {
        Route::namespace('Language')
            ->group(function () {
                Route::get('create', 'Create')->name('create');
                Route::post('', 'Store')->name('store');
                Route::get('{localisation}/edit', 'Edit')->name('edit');
                Route::patch('{localisation}', 'Update')->name('update');
                Route::delete('{localisation}', 'Destroy')->name('destroy');

                Route::get('initTable', 'InitTable')->name('initTable');
                Route::get('tableData', 'TableData')->name('tableData');
                Route::get('exportExcel', 'ExportExcel')->name('exportExcel');
            });

        Route::namespace('Json')
            ->group(function () {
                Route::get('editTexts', 'Index')->name('editTexts');
                Route::get('getLangFile/{language}/{subDir}', 'Edit')->name('getLangFile');
                Route::patch('saveLangFile/{language}/{subDir}', 'Update')->name('saveLangFile');
                Route::patch('addKey', 'AddKey')->name('addKey');
                Route::patch('merge/{locale?}', 'Merge')->name('merge');
            });
    });
