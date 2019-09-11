<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Collection;

if (config('twill.enabled.users-management')) {
    Route::module('users', ['except' => ['sort', 'feature']]);
}

if (config('twill.enabled.media-library')) {
    Route::group(['prefix' => 'media-library', 'as' => 'media-library.'], function () {
        Route::post('sign-s3-upload', ['as' => 'sign-s3-upload', 'uses' => 'MediaLibraryController@signS3Upload']);
        Route::put('medias/single-update', ['as' => 'medias.single-update', 'uses' => 'MediaLibraryController@singleUpdate']);
        Route::put('medias/bulk-update', ['as' => 'medias.bulk-update', 'uses' => 'MediaLibraryController@bulkUpdate']);
        Route::put('medias/bulk-delete', ['as' => 'medias.bulk-delete', 'uses' => 'MediaLibraryController@bulkDelete']);
        Route::get('medias/tags', ['as' => 'medias.tags', 'uses' => 'MediaLibraryController@tags']);
        Route::resource('medias', 'MediaLibraryController', ['only' => ['index', 'store', 'destroy']]);
    });
}

if (config('twill.enabled.file-library')) {
    Route::group(['prefix' => 'file-library', 'as' => 'file-library.'], function () {
        Route::post('sign-s3-upload', ['as' => 'sign-s3-upload', 'uses' => 'FileLibraryController@signS3Upload']);
        Route::put('files/single-update', ['as' => 'files.single-update', 'uses' => 'FileLibraryController@singleUpdate']);
        Route::put('files/bulk-update', ['as' => 'files.bulk-update', 'uses' => 'FileLibraryController@bulkUpdate']);
        Route::put('files/bulk-delete', ['as' => 'files.bulk-delete', 'uses' => 'FileLibraryController@bulkDelete']);
        Route::get('files/tags', ['as' => 'files.tags', 'uses' => 'FileLibraryController@tags']);
        Route::resource('files', 'FileLibraryController', ['only' => ['index', 'store', 'destroy']]);
    });
}

if (config('twill.enabled.block-editor')) {
    Route::post('blocks/preview', ['as' => 'blocks.preview', 'uses' => 'BlocksController@preview']);
}

if (config('twill.enabled.buckets')) {
    $bucketsRoutes = config('twill.bucketsRoutes') ?? Collection::make(config('twill.buckets'))->mapWithKeys(function ($bucketSection, $bucketSectionKey) {
        return [$bucketSectionKey => 'featured'];
    })->toArray();

    foreach ($bucketsRoutes as $bucketSectionKey => $routePrefix) {
        Route::group(['prefix' => str_replace(".","/",$routePrefix), 'as' => $routePrefix . '.'], function () use ($bucketSectionKey) {
            Route::get($bucketSectionKey, ['as' => $bucketSectionKey, 'uses' => 'FeaturedController@index']);
            Route::group(['prefix' => $bucketSectionKey, 'as' => $bucketSectionKey . '.'], function () {
                Route::post('save', ['as' => 'save', 'uses' => 'FeaturedController@save']);
            });

        });
    }
}

if (config('twill.enabled.settings')) {
    Route::name('settings')->get('/settings/{section}', 'SettingController@index');
    Route::name('settings.update')->post('/settings/{section}', 'SettingController@update');
}

if (config('twill.enabled.dashboard')) {
    Route::name('dashboard')->get('/', 'DashboardController@index');
}

if (config('twill.enabled.search')) {
    Route::name('search')->get('search', 'DashboardController@search');
}
