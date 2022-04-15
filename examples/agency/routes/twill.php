<?php

use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'about'], function () {
    Route::module('roles');
    Route::module('people');
    Route::module('personVideos');
    Route::singleton('about');
});

Route::group(['prefix' => 'contact'], function () {
    Route::module('offices');
});

Route::group(['prefix' => 'work'], function () {
    Route::module('sectors');
    Route::module('disciplines');
});
