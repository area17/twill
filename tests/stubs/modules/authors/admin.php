<?php

Route::group(['prefix' => 'personnel'], function () {
    Route::twillModule('authors');
});

Route::twillModule('categories');
