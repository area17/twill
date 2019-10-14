<?php

namespace A17\Twill\Tests\Integration;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;

class RoutesTest extends TestCase
{
    const ROUTES = [
        '/',
        'blocks/preview',
        'file-library/files',
        'file-library/files/bulk-delete',
        'file-library/files/bulk-update',
        'file-library/files/single-update',
        'file-library/files/tags',
        'file-library/files/{file}',
        'file-library/sign-s3-upload',
        'login',
        'logout',
        'media-library/medias',
        'media-library/medias/bulk-delete',
        'media-library/medias/bulk-update',
        'media-library/medias/single-update',
        'media-library/medias/tags',
        'media-library/medias/{media}',
        'media-library/sign-s3-upload',
        'password/email',
        'password/reset',
        'password/reset/{token}',
        'password/welcome/{token}',
        'search',
        'settings/{section}',
        'templates',
        'templates/xhr/{view}',
        'templates/{view}',
        'users',
        'users/browser',
        'users/bulkDelete',
        'users/bulkFeature',
        'users/bulkPublish',
        'users/bulkRestore',
        'users/create',
        'users/impersonate/stop',
        'users/impersonate/{id}',
        'users/preview/{id}',
        'users/publish',
        'users/reorder',
        'users/restore',
        'users/restoreRevision/{id}',
        'users/tags',
        'users/{user}',
        'users/{user}/edit',
    ];

    public function testRoutesList()
    {
        $this->assertEquals(static::ROUTES, $this->getAllRoutes()->toArray());
    }

    public function getAllRoutes()
    {
        $routes = collect();

        foreach (Route::getRoutes()->getIterator() as $route) {
            if (Str::startsWith($route->action['uses'], 'A17\Twill')) {
                $routes->push($route->uri);
            }
        }

        return $routes->sort()->unique();
    }

    public function dumpRoutes()
    {
        // This is only a helper to dump he current list of routes

        dd(
            $this->getAllRoutes()
                ->values()
                ->toArray()
        );
    }
}
