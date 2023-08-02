<?php

use Illuminate\Support\Facades\Route;


Route::twillSingleton('about');

Route::group(['prefix' => 'about'], function () {
    Route::module('roles');
    Route::module('people');
    Route::module('personVideos');
});

Route::group(['prefix' => 'contact'], function () {
    Route::module('offices');
});

Route::group(['prefix' => 'work'], function () {
    Route::module('sectors');
    Route::module('disciplines');
    Route::module('works');
    Route::module('workLinks');
});
