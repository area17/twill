<?php

Route::module('users', ['except' => ['sort', 'feature', 'bucket', 'browser', 'file']]);

Route::group(['prefix' => 'media-library', 'as' => 'media-library.'], function () {
    Route::post('sign-s3-upload', ['as' => 'sign-s3-upload', 'uses' => 'MediaLibraryController@signS3Upload']);
    Route::get('medias/bulk-edit', ['as' => 'medias.bulk-edit', 'uses' => 'MediaLibraryController@bulkEdit']);
    Route::put('medias/single-update', ['as' => 'medias.single-update', 'uses' => 'MediaLibraryController@singleUpdate']);
    Route::put('medias/bulk-update', ['as' => 'medias.bulk-update', 'uses' => 'MediaLibraryController@bulkUpdate']);
    Route::get('medias/tags', ['as' => 'medias.tags', 'uses' => 'MediaLibraryController@tags']);
    Route::get('medias/thumbnail', ['as' => 'medias.thumbnail', 'uses' => 'MediaLibraryController@thumbnail']);
    Route::get('medias/crop', ['as' => 'medias.crop', 'uses' => 'MediaLibraryController@crop']);
    Route::resource('medias', 'MediaLibraryController', ['only' => ['index', 'edit', 'store', 'destroy']]);
});

Route::group(['prefix' => 'file-library', 'as' => 'file-library.'], function () {
    Route::post('sign-s3-upload', ['as' => 'sign-s3-upload', 'uses' => 'FileLibraryController@signS3Upload']);
    Route::get('file/bulk-edit', ['as' => 'files.bulk-edit', 'uses' => 'FileLibraryController@bulkEdit']);
    Route::put('file/single-update', ['as' => 'files.single-update', 'uses' => 'FileLibraryController@singleUpdate']);
    Route::put('files/bulk-update', ['as' => 'files.bulk-update', 'uses' => 'FileLibraryController@bulkUpdate']);
    Route::get('files/tags', ['as' => 'files.tags', 'uses' => 'FileLibraryController@tags']);
    Route::resource('files', 'FileLibraryController', ['only' => ['index', 'edit', 'store', 'destroy']]);
});

Route::post('blocks/preview', ['as' => 'blocks.preview', 'uses' => 'BlocksController@preview']);
