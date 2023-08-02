<?php

use LaravelJsonApi\Laravel\Facades\JsonApiRoute;
use LaravelJsonApi\Laravel\Http\Controllers\JsonApiController;


JsonApiRoute::server('v1')
    ->prefix('v1')
    ->resources(function ($server) {
        $server->resource('works', '\\' . JsonApiController::class)->readOnly()
            ->relationships(function ($relationships) {
                $relationships->hasMany('blocks');
                $relationships->hasMany('media');
                $relationships->hasMany('files');
                $relationships->hasMany('related-items');
                $relationships->hasMany('sectors');
                $relationships->hasMany('disciplines');
            });

        $server->resource('sectors', '\\' . JsonApiController::class)
            ->relationships(function ($relationships) {
                 $relationships->hasMany('works');
            });

        $server->resource('disciplines', '\\' . JsonApiController::class)
            ->relationships(function ($relationships) {
                $relationships->hasMany('works');
            });

        $server->resource('offices', '\\' . JsonApiController::class)
            ->relationships(function ($relationships) {
                $relationships->hasMany('media');
            });

        $server->resource('abouts', '\\' . JsonApiController::class);

        $server->resource('people', '\\' . JsonApiController::class)
            ->relationships(function ($relationships) {
                $relationships->hasMany('media');
                $relationships->hasMany('works');
                $relationships->hasOne('office');
                $relationships->hasMany('videos');
            });
    });

