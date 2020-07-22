<?php

use Illuminate\Support\Facades\Route;

Route::namespace('Language')
    ->group(function () {
        Route::get('create', 'Create')->name('create');
        Route::post('', 'Store')->name('store');
        Route::get('{language}/edit', 'Edit')->name('edit');
        Route::patch('{language}', 'Update')->name('update');
        Route::delete('{language}', 'Destroy')->name('destroy');

        Route::get('initTable', 'InitTable')->name('initTable');
        Route::get('tableData', 'TableData')->name('tableData');
        Route::get('exportExcel', 'ExportExcel')->name('exportExcel');
        Route::get('options', 'Options')->name('options');
    });
