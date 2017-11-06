<?php

if (config('cms-toolkit.enabled.users-management')) {
    Route::module('users', ['except' => ['sort', 'feature']]);
}

if (config('cms-toolkit.enabled.media-library')) {
    Route::group(['prefix' => 'media-library', 'as' => 'media-library.'], function () {
        Route::post('sign-s3-upload', ['as' => 'sign-s3-upload', 'uses' => 'MediaLibraryController@signS3Upload']);
        Route::get('medias/bulk-edit', ['as' => 'medias.bulk-edit', 'uses' => 'MediaLibraryController@bulkEdit']);
        Route::put('medias/single-update', ['as' => 'medias.single-update', 'uses' => 'MediaLibraryController@singleUpdate']);
        Route::put('medias/bulk-update', ['as' => 'medias.bulk-update', 'uses' => 'MediaLibraryController@bulkUpdate']);
        Route::get('medias/tags', ['as' => 'medias.tags', 'uses' => 'MediaLibraryController@tags']);
        Route::resource('medias', 'MediaLibraryController', ['only' => ['index', 'edit', 'store', 'destroy']]);
    });
}

if (config('cms-toolkit.enabled.file-library')) {
    Route::group(['prefix' => 'file-library', 'as' => 'file-library.'], function () {
        Route::post('sign-s3-upload', ['as' => 'sign-s3-upload', 'uses' => 'FileLibraryController@signS3Upload']);
        Route::get('file/bulk-edit', ['as' => 'files.bulk-edit', 'uses' => 'FileLibraryController@bulkEdit']);
        Route::put('file/single-update', ['as' => 'files.single-update', 'uses' => 'FileLibraryController@singleUpdate']);
        Route::put('files/bulk-update', ['as' => 'files.bulk-update', 'uses' => 'FileLibraryController@bulkUpdate']);
        Route::get('files/tags', ['as' => 'files.tags', 'uses' => 'FileLibraryController@tags']);
        Route::resource('files', 'FileLibraryController', ['only' => ['index', 'edit', 'store', 'destroy']]);
    });
}

if (config('cms-toolkit.enabled.block-editor')) {
    Route::post('blocks/preview', ['as' => 'blocks.preview', 'uses' => 'BlocksController@preview']);
}

if (config('cms-toolkit.enabled.buckets')) {
    Route::group(['prefix' => 'featured', 'as' => 'featured.'], function () {
        collect(config('cms-toolkit.buckets'))->each(function ($bucketSection, $bucketSectionKey) {
            Route::get($bucketSectionKey, ['as' => $bucketSectionKey, 'uses' => 'FeaturedController@index']);
            Route::group(['prefix' => $bucketSectionKey, 'as' => $bucketSectionKey . '.'], function () {
                Route::post('{bucket}', ['as' => 'add', 'uses' => 'FeaturedController@add']);
                Route::delete('{bucket}', ['as' => 'remove', 'uses' => 'FeaturedController@remove']);
                Route::post('{bucket}/sortable', ['as' => 'sortable', 'uses' => 'FeaturedController@sortable']);
                //TODO this is going to happen on add/remove/sort now that we don't have a save button anymore, or should we have one?
                Route::get('save', ['as' => 'save', 'uses' => 'FeaturedController@save']);
                Route::get('cancel', ['as' => 'cancel', 'uses' => 'FeaturedController@cancel']);
            });
        });
    });
}

if (config('cms-toolkit.enabled.settings')) {
    Route::name('settings')->get('/settings/{section}', 'SettingController@index');
    Route::name('settings.update')->post('/settings/{section}', 'SettingController@update');
}
