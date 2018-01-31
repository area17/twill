<?php

if (config('cms-toolkit.enabled.users-management')) {
    Route::module('users', ['except' => ['sort', 'feature']]);
}

if (config('cms-toolkit.enabled.media-library')) {
    Route::group(['prefix' => 'media-library', 'as' => 'media-library.'], function () {
        Route::post('sign-s3-upload', ['as' => 'sign-s3-upload', 'uses' => 'MediaLibraryController@signS3Upload']);
        Route::put('medias/single-update', ['as' => 'medias.single-update', 'uses' => 'MediaLibraryController@singleUpdate']);
        Route::put('medias/bulk-update', ['as' => 'medias.bulk-update', 'uses' => 'MediaLibraryController@bulkUpdate']);
        Route::put('medias/bulk-delete', ['as' => 'medias.bulk-delete', 'uses' => 'MediaLibraryController@bulkDelete']);
        Route::get('medias/tags', ['as' => 'medias.tags', 'uses' => 'MediaLibraryController@tags']);
        Route::resource('medias', 'MediaLibraryController', ['only' => ['index', 'store', 'destroy']]);
    });
}

if (config('cms-toolkit.enabled.file-library')) {
    Route::group(['prefix' => 'file-library', 'as' => 'file-library.'], function () {
        Route::post('sign-s3-upload', ['as' => 'sign-s3-upload', 'uses' => 'FileLibraryController@signS3Upload']);
        Route::put('file/single-update', ['as' => 'files.single-update', 'uses' => 'FileLibraryController@singleUpdate']);
        Route::put('files/bulk-update', ['as' => 'files.bulk-update', 'uses' => 'FileLibraryController@bulkUpdate']);
        Route::put('files/bulk-delete', ['as' => 'files.bulk-delete', 'uses' => 'FileLibraryController@bulkDelete']);
        Route::get('files/tags', ['as' => 'files.tags', 'uses' => 'FileLibraryController@tags']);
        Route::resource('files', 'FileLibraryController', ['only' => ['index', 'store', 'destroy']]);
    });
}

if (config('cms-toolkit.enabled.block-editor')) {
    Route::post('blocks/preview', ['as' => 'blocks.preview', 'uses' => 'BlocksController@preview']);
}

if (config('cms-toolkit.enabled.buckets')) {
    $bucketsRoutes = config('cms-toolkit.bucketsRoutes') ?? collect(config('cms-toolkit.buckets'))->mapWithKeys(function ($bucketSection, $bucketSectionKey) {
        return [$bucketSectionKey => 'featured'];
    })->toArray();

    foreach ($bucketsRoutes as $bucketSectionKey => $routePrefix) {
        Route::group(['prefix' => $routePrefix, 'as' => $routePrefix . '.'], function () use ($bucketSectionKey) {
            Route::get($bucketSectionKey, ['as' => $bucketSectionKey, 'uses' => 'FeaturedController@index']);
            Route::group(['prefix' => $bucketSectionKey, 'as' => $bucketSectionKey . '.'], function () {
                Route::put('{bucket}', ['as' => 'add', 'uses' => 'FeaturedController@add']);
                Route::put('{bucket}/remove', ['as' => 'remove', 'uses' => 'FeaturedController@remove']);
                Route::put('{bucket}/sortable', ['as' => 'sortable', 'uses' => 'FeaturedController@sortable']);
                Route::put('{bucket}/feature', ['as' => 'feature', 'uses' => 'FeaturedController@feature']);
            });

        });
    }
}

if (config('cms-toolkit.enabled.settings')) {
    Route::name('settings')->get('/settings/{section}', 'SettingController@index');
    Route::name('settings.update')->post('/settings/{section}', 'SettingController@update');
}
