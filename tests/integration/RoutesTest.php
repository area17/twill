<?php

namespace A17\Twill\Tests\Integration;

class RoutesTest extends TestCase
{
    const ROUTES = [
        'storage/media-library/{path}',
        'twill',
        'twill/blocks/preview',
        'twill/file-library/files',
        'twill/file-library/files/bulk-delete',
        'twill/file-library/files/bulk-update',
        'twill/file-library/files/single-update',
        'twill/file-library/files/tags',
        'twill/file-library/files/{file}',
        'twill/file-library/sign-s3-upload',
        'twill/login',
        'twill/login-2fa',
        'twill/logout',
        'twill/media-library/medias',
        'twill/media-library/medias/bulk-delete',
        'twill/media-library/medias/bulk-update',
        'twill/media-library/medias/single-update',
        'twill/media-library/medias/tags',
        'twill/media-library/medias/{media}',
        'twill/media-library/sign-s3-upload',
        'twill/password/email',
        'twill/password/reset',
        'twill/password/reset/{token}',
        'twill/password/welcome/{token}',
        'twill/search',
        'twill/settings/{section}',
        'twill/templates',
        'twill/templates/xhr/{view}',
        'twill/templates/{view}',
        'twill/users',
        'twill/users/browser',
        'twill/users/bulkDelete',
        'twill/users/bulkFeature',
        'twill/users/bulkPublish',
        'twill/users/bulkRestore',
        'twill/users/create',
        'twill/users/impersonate/stop',
        'twill/users/impersonate/{id}',
        'twill/users/preview/{id}',
        'twill/users/publish',
        'twill/users/reorder',
        'twill/users/restore',
        'twill/users/restoreRevision/{id}',
        'twill/users/tags',
        'twill/users/{user}',
        'twill/users/{user}/edit',
    ];

    public function testCanListAllRoutes()
    {
        $this->assertEquals(static::ROUTES, $this->getAllUris()->toArray());
    }

    /**
     * dd Routes
     */
    public function ddRoutes()
    {
        // This is only a helper to dump and die the current list of routes

        dd($this->getAllUris()->toArray());
    }
}
