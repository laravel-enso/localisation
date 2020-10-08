<?php

use Illuminate\Support\Facades\Route;
use LaravelEnso\Localisation\Http\Controllers\Language\Create;
use LaravelEnso\Localisation\Http\Controllers\Language\Destroy;
use LaravelEnso\Localisation\Http\Controllers\Language\Edit;
use LaravelEnso\Localisation\Http\Controllers\Language\ExportExcel;
use LaravelEnso\Localisation\Http\Controllers\Language\InitTable;
use LaravelEnso\Localisation\Http\Controllers\Language\Options;
use LaravelEnso\Localisation\Http\Controllers\Language\Store;
use LaravelEnso\Localisation\Http\Controllers\Language\TableData;
use LaravelEnso\Localisation\Http\Controllers\Language\Update;

Route::get('create', Create::class)->name('create');
Route::post('', Store::class)->name('store');
Route::get('{language}/edit', Edit::class)->name('edit');
Route::patch('{language}', Update::class)->name('update');
Route::delete('{language}', Destroy::class)->name('destroy');

Route::get('initTable', InitTable::class)->name('initTable');
Route::get('tableData', TableData::class)->name('tableData');
Route::get('exportExcel', ExportExcel::class)->name('exportExcel');
Route::get('options', Options::class)->name('options');
