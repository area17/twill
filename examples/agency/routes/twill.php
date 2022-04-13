<?php

use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'about'], function () {
    Route::module('roles');
});

Route::group(['prefix' => 'contact'], function () {
    Route::module('offices');
});

