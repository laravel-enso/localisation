<?php

use Illuminate\Support\Facades\Route;
use LaravelEnso\Localisation\Http\Controllers\Json\AddKey;
use LaravelEnso\Localisation\Http\Controllers\Json\Edit;
use LaravelEnso\Localisation\Http\Controllers\Json\Index;
use LaravelEnso\Localisation\Http\Controllers\Json\Merge;
use LaravelEnso\Localisation\Http\Controllers\Json\Update;

Route::get('editTexts', Index::class)->name('editTexts');
Route::get('getLangFile/{language}/{subDir}', Edit::class)->name('getLangFile');
Route::patch('saveLangFile/{language}/{subDir}', Update::class)->name('saveLangFile');
Route::patch('addKey', AddKey::class)->name('addKey');
Route::patch('merge/{locale?}', Merge::class)->name('merge');
