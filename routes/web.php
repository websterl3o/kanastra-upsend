<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('collection-lists.create');
});

Route::group(['prefix' => 'collection-lists'], function () {
    Route::get('/', 'App\Http\Controllers\CollectionListController@index')->name('collection-lists.index');
    Route::get('/create', 'App\Http\Controllers\CollectionListController@create')->name('collection-lists.create');
    Route::post('/', 'App\Http\Controllers\CollectionListController@store')->name('collection-lists.store');
    Route::get('/{collection_list}', 'App\Http\Controllers\CollectionListController@show')->name('collection-lists.show');
    Route::get('/{collection_list}/edit', 'App\Http\Controllers\CollectionListController@edit')->name('collection-lists.edit');
    Route::put('/{collection_list}', 'App\Http\Controllers\CollectionListController@update')->name('collection-lists.update');
    Route::delete('/{collection_list}', 'App\Http\Controllers\CollectionListController@destroy')->name('collection-lists.destroy');
});
