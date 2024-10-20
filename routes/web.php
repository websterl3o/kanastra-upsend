<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

Route::get('/', function () {
    return redirect()->route('collection-lists.create');
});

Route::group(['prefix' => 'collection-lists'], function () {
    Route::get('/create', 'App\Http\Controllers\CollectionListController@create')->name('collection-lists.create');
    Route::post('/', 'App\Http\Controllers\CollectionListController@store')->name('collection-lists.store')->withoutMiddleware([VerifyCsrfToken::class]);
});
