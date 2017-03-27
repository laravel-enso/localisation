<?php

Route::group(['namespace' => 'LaravelEnso\Localisation\app\Http\Controllers', 'middleware' => ['web', 'auth', 'core']], function () {
    Route::group(['prefix' => 'system/localisation', 'as' => 'system.localisation.'], function () {
        Route::get('editTexts', 'LocalisationController@editTexts')->name('editTexts');
        Route::get('getLangFile/{locale}', 'LocalisationController@getLangFile')->name('getLangFile');
        Route::patch('saveLangFile', 'LocalisationController@saveLangFile')->name('saveLangFile');
        Route::get('initTable', 'LocalisationController@initTable')->name('initTable');
        Route::get('getTableData', 'LocalisationController@getTableData')->name('getTableData');
    });

    Route::group(['prefix' => 'system', 'as' => 'system.'], function () {
        Route::resource('localisation', 'LocalisationController');
    });
});
