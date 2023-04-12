<?php

use A17\Twill\Facades\TwillRoutes;

Route::group(['prefix' => 'personnel'], function () {
    TwillRoutes::module('authors');
});

TwillRoutes::module('categories');
