<?php

namespace A17\Twill\Tests\Integration;

class RoutesTest extends TestCase
{
    const ROUTES = [
        'storage/media-library/{path}',
        'twill',
        'twill/blocks/preview',
        'twill/categories',
        'twill/categories/browser',
        'twill/categories/bulkDelete',
        'twill/categories/bulkFeature',
        'twill/categories/bulkForceDelete',
        'twill/categories/bulkPublish',
        'twill/categories/bulkRestore',
        'twill/categories/create',
        'twill/categories/duplicate/{id}',
        'twill/categories/feature',
        'twill/categories/forceDelete',
        'twill/categories/preview/{id}',
        'twill/categories/publish',
        'twill/categories/reorder',
        'twill/categories/restore',
        'twill/categories/restoreRevision/{id}',
        'twill/categories/tags',
        'twill/categories/{category}',
        'twill/categories/{category}/edit',
        'twill/file-library/files',
        'twill/file-library/files/bulk-delete',
        'twill/file-library/files/bulk-update',
        'twill/file-library/files/single-update',
        'twill/file-library/files/tags',
        'twill/file-library/files/{file}',
        'twill/file-library/sign-azure-upload',
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
        'twill/media-library/sign-azure-upload',
        'twill/media-library/sign-s3-upload',
        'twill/password/email',
        'twill/password/reset',
        'twill/password/reset/{token}',
        'twill/password/welcome/{token}',
        'twill/personnel/authors',
        'twill/personnel/authors/browser',
        'twill/personnel/authors/bulkDelete',
        'twill/personnel/authors/bulkFeature',
        'twill/personnel/authors/bulkForceDelete',
        'twill/personnel/authors/bulkPublish',
        'twill/personnel/authors/bulkRestore',
        'twill/personnel/authors/create',
        'twill/personnel/authors/duplicate/{id}',
        'twill/personnel/authors/feature',
        'twill/personnel/authors/forceDelete',
        'twill/personnel/authors/preview/{id}',
        'twill/personnel/authors/publish',
        'twill/personnel/authors/reorder',
        'twill/personnel/authors/restore',
        'twill/personnel/authors/restoreRevision/{id}',
        'twill/personnel/authors/tags',
        'twill/personnel/authors/{author}',
        'twill/personnel/authors/{author}/edit',
        'twill/search',
        'twill/settings/{section}',
        'twill/templates',
        'twill/templates/xhr/{view}',
        'twill/templates/{view}',
        'twill/users',
        'twill/users/browser',
        'twill/users/bulkDelete',
        'twill/users/bulkFeature',
        'twill/users/bulkForceDelete',
        'twill/users/bulkPublish',
        'twill/users/bulkRestore',
        'twill/users/create',
        'twill/users/duplicate/{id}',
        'twill/users/forceDelete',
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
