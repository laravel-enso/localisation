<?php

Route::namespace('Json')
    ->group(function () {
        Route::get('editTexts', 'Index')->name('editTexts');
        Route::get('getLangFile/{language}/{subDir}', 'Edit')->name('getLangFile');
        Route::patch('saveLangFile/{language}/{subDir}', 'Update')->name('saveLangFile');
        Route::patch('addKey', 'AddKey')->name('addKey');
        Route::patch('merge/{locale?}', 'Merge')->name('merge');
    });
