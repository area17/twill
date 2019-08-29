<?php

use Illuminate\Support\Facades\Route;

Route::get('templates', ['as' => 'templates.index', 'uses' => 'TemplatesController@index']);
Route::get('templates/xhr/{view}', ['as' => 'templates.xhr', 'uses' => 'TemplatesController@xhr']);
Route::get('templates/{view}', ['as' => 'templates.view', 'uses' => 'TemplatesController@view']);
