<?php

Route::group(['middleware' => ['web']], function () {
    Route::prefix('npa')->group(function () {

        Route::get('/design', 'Iateadonut\NPAPayProcessing\Http\Controllers\NPAController@design')->name('npa.design');

        Route::get('/redirect', 'Iateadonut\NPAPayProcessing\Http\Controllers\NPAController@redirect')->name('npa.redirect');

        Route::get('/success', 'Iateadonut\NPAPayProcessing\Http\Controllers\NPAController@success')->name('npa.success');

        Route::get('/cancel', 'Iateadonut\NPAPayProcessing\Http\Controllers\NPAController@cancel')->name('npa.cancel');

        Route::post('/submit_to_npa', 'Iateadonut\NPAPayProcessing\Http\Controllers\NPAController@submit_to_npa')->name('npa.submit_to_npa');

        Route::get('/testcart', 'Iateadonut\NPAPayProcessing\Http\Controllers\NPAController@test_cart');
    });
});


