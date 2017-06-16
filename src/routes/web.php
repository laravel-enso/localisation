<?php

Route::group([
    'namespace' => 'LaravelEnso\Localisation\app\Http\Controllers',
    'middleware' => ['web', 'auth', 'core']
], function () {
    Route::group(['prefix' => 'system/localisation', 'as' => 'system.localisation.'], function () {
        Route::get('initTable', 'LocalisationController@initTable')->name('initTable');
        Route::get('getTableData', 'LocalisationController@getTableData')->name('getTableData');

        Route::get('editTexts', 'LangFileController@editTexts')->name('editTexts');
        Route::get('getLangFile/{locale}', 'LangFileController@getLangFile')->name('getLangFile');
        Route::patch('saveLangFile', 'LangFileController@saveLangFile')->name('saveLangFile');
    });

    Route::group(['prefix' => 'system', 'as' => 'system.'], function () {
        Route::resource('localisation', 'LocalisationController');
    });
});
