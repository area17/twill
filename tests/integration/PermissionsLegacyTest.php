<?php

namespace A17\Twill\Tests\Integration;

class PermissionsLegacyTest extends PermissionsTestBase
{
    protected function getPackageProviders($app)
    {
        // This config must be set before loading TwillServiceProvider to select
        // between AuthServiceProvider and PermissionAuthServiceProvider
        $app['config']->set('twill.enabled.permissions-management', false);

        return parent::getPackageProviders($app);
    }

    public function createUser($role)
    {
        $user = $this->makeUser();
        $user->role = $role;
        $user->save();

        return $user;
    }

    // FIXME — this is needed for the new admin routes to take effect in the next test,
    // because files are copied in `setUp()` after the app is initialized.
    public function testDummy()
    {
        $this->assertTrue(true);
    }

    public function testViewOnlyPermissions()
    {
        $admin = $this->createUser('ADMIN');
        $guest = $this->createUser('VIEWONLY');

        // User is logged in
        $this->loginUser($guest);

        // User can access the Media Library
        $this->httpRequestAssert('/twill/media-library/medias?page=1&type=image', 'GET', [], 200);
        $this->httpRequestAssert('/twill/file-library/files?page=1', 'GET', [], 200);

        // User can't upload medias
        $this->httpRequestAssert('/twill/media-library/medias', 'POST', [], 403);
        $this->httpRequestAssert('/twill/file-library/files', 'POST', [], 403);

        // User can't access settings
        $this->httpRequestAssert('/twill/settings/seo', 'GET', [], 403);

        // User can't access users list
        $this->httpRequestAssert("/twill/users", 'GET', [], 403);

        // User can't access other profiles
        $this->httpRequestAssert("/twill/users/{$admin->id}/edit", 'GET', [], 403);

        // User can access own profile
        $this->httpRequestAssert("/twill/users/{$guest->id}/edit", 'GET', [], 200);


        $posting = $this->createPosting();

        // User can access items list
        $this->httpRequestAssert("/twill/postings", 'GET', [], 200);

        // User can't access item details
        $this->httpRequestAssert("/twill/postings/{$posting->id}/edit", 'GET', [], 403);

        // User can't create items
        $this->httpRequestAssert('/twill/postings', 'POST', [], 403);
    }

    public function testPublisherPermissions()
    {
        $guest = $this->createUser('VIEWONLY');
        $publisher = $this->createUser('PUBLISHER');
        $admin = $this->createUser('ADMIN');

        // User is logged in
        $this->loginUser($publisher);

        // User can access the Media Library
        $this->httpRequestAssert('/twill/media-library/medias?page=1&type=image', 'GET', [], 200);
        $this->httpRequestAssert('/twill/file-library/files?page=1', 'GET', [], 200);

        // User can upload medias
        $this->httpRequestAssert('/twill/media-library/medias', 'POST', [], 200);
        $this->httpRequestAssert('/twill/file-library/files', 'POST', [], 200);

        // User can access settings
        $this->httpRequestAssert('/twill/settings/seo', 'GET', [], 200);

        // User can't access users list
        $this->httpRequestAssert("/twill/users", 'GET', [], 403);

        // User can't access other profiles
        $this->httpRequestAssert("/twill/users/{$guest->id}/edit", 'GET', [], 403);
        $this->httpRequestAssert("/twill/users/{$admin->id}/edit", 'GET', [], 403);

        // User can access own profile
        $this->httpRequestAssert("/twill/users/{$publisher->id}/edit", 'GET', [], 200);


        $posting = $this->createPosting();

        // User can access items list
        $this->httpRequestAssert("/twill/postings", 'GET', [], 200);

        // User can access item details
        $this->httpRequestAssert("/twill/postings/{$posting->id}/edit", 'GET', [], 200);

        // User can create items
        $this->httpRequestAssert('/twill/postings', 'POST', [], 200);
    }

    public function testAdminPermissions()
    {
        $guest = $this->createUser('VIEWONLY');
        $publisher = $this->createUser('PUBLISHER');
        $admin = $this->createUser('ADMIN');

        // User is logged in
        $this->loginUser($admin);

        // User can access the Media Library
        $this->httpRequestAssert('/twill/media-library/medias?page=1&type=image', 'GET', [], 200);
        $this->httpRequestAssert('/twill/file-library/files?page=1', 'GET', [], 200);

        // User can upload medias
        $this->httpRequestAssert('/twill/media-library/medias', 'POST', [], 200);
        $this->httpRequestAssert('/twill/file-library/files', 'POST', [], 200);

        // User can access settings
        $this->httpRequestAssert('/twill/settings/seo', 'GET', [], 200);

        // User can access users list
        $this->httpRequestAssert("/twill/users", 'GET', [], 200);

        // User can access other profiles
        $this->httpRequestAssert("/twill/users/{$guest->id}/edit", 'GET', [], 200);
        $this->httpRequestAssert("/twill/users/{$publisher->id}/edit", 'GET', [], 200);

        // User can access own profile
        $this->httpRequestAssert("/twill/users/{$admin->id}/edit", 'GET', [], 200);


        $posting = $this->createPosting();

        // User can access items list
        $this->httpRequestAssert("/twill/postings", 'GET', [], 200);

        // User can access item details
        $this->httpRequestAssert("/twill/postings/{$posting->id}/edit", 'GET', [], 200);

        // User can create items
        $this->httpRequestAssert('/twill/postings', 'POST', [], 200);
    }
}
