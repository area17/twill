<?php

namespace A17\Twill\Tests\Integration;

use A17\Twill\Models\User;
use App\Repositories\AuthorRepository;
use Illuminate\Support\Facades\Hash;

class PermissionsLegacyTest extends PermissionsTestBase
{
    public function configTwill($app)
    {
        parent::configTwill($app);

        $app['config']->set('twill.enabled.permissions-management', false);
        $app['config']->set('twill.enabled.settings', true);
    }

    public function createUser($role)
    {
        $user = User::make([
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'role' => $role,
            'published' => true,
        ]);
        $user->password = Hash::make($user->email);
        $user->save();

        return $user;
    }

    public function createAuthor()
    {
        $author = app(AuthorRepository::class)->create([
            'published' => true,
            'name' => $this->faker->name,
        ]);

        return $author;
    }

    public function testViewOnlyPermissions()
    {
        $admin = $this->createUser('ADMIN');
        $guest = $this->createUser('VIEWONLY');

        // User is logged in
        $this->loginUser($guest);
        $this->httpRequestAssert('/twill');
        $this->assertSee($guest->name);

        // User can access the Media Library
        $this->httpRequestAssert('/twill/media-library/medias?page=1&type=image', 'GET', [], 200);

        // User can't upload medias
        $this->httpRequestAssert('/twill/media-library/medias', 'POST', [], 403);

        // User can't access settings
        $this->httpRequestAssert('/twill/settings/seo', 'GET', [], 403);

        // User can't access users list
        $this->httpRequestAssert("/twill/users", 'GET', [], 403);

        // User can't access other profiles
        $this->httpRequestAssert("/twill/users/{$admin->id}/edit", 'GET', [], 403);

        // User can access own profile
        $this->httpRequestAssert("/twill/users/{$guest->id}/edit", 'GET', [], 200);


        $author = $this->createAuthor();

        // User can access authors list
        $this->httpRequestAssert("/twill/personnel/authors", 'GET', [], 200);

        // User can't access author details
        $this->httpRequestAssert("/twill/personnel/authors/{$author->id}/edit", 'GET', [], 403);

        // User can't create authors
        $this->httpRequestAssert('/twill/personnel/authors', 'POST', [], 403);
    }

    public function testPublisherPermissions()
    {
        $guest = $this->createUser('VIEWONLY');
        $publisher = $this->createUser('PUBLISHER');
        $admin = $this->createUser('ADMIN');

        // User is logged in
        $this->loginUser($publisher);
        $this->httpRequestAssert('/twill');
        $this->assertSee($publisher->name);

        // User can access the Media Library
        $this->httpRequestAssert('/twill/media-library/medias?page=1&type=image', 'GET', [], 200);

        // User can upload medias
        $this->httpRequestAssert('/twill/media-library/medias', 'POST', [], 200);

        // User can access settings
        $this->httpRequestAssert('/twill/settings/seo', 'GET', [], 200);

        // User can't access users list
        $this->httpRequestAssert("/twill/users", 'GET', [], 403);

        // User can't access other profiles
        $this->httpRequestAssert("/twill/users/{$guest->id}/edit", 'GET', [], 403);
        $this->httpRequestAssert("/twill/users/{$admin->id}/edit", 'GET', [], 403);

        // User can access own profile
        $this->httpRequestAssert("/twill/users/{$publisher->id}/edit", 'GET', [], 200);


        $author = $this->createAuthor();

        // User can access authors list
        $this->httpRequestAssert("/twill/personnel/authors", 'GET', [], 200);

        // User can access author details
        $this->httpRequestAssert("/twill/personnel/authors/{$author->id}/edit", 'GET', [], 200);

        // User can create authors
        $this->httpRequestAssert('/twill/personnel/authors', 'POST', [], 200);
    }

    public function testAdminPermissions()
    {
        $guest = $this->createUser('VIEWONLY');
        $publisher = $this->createUser('PUBLISHER');
        $admin = $this->createUser('ADMIN');

        // User is logged in
        $this->loginUser($admin);
        $this->httpRequestAssert('/twill');
        $this->assertSee($admin->name);

        // User can access the Media Library
        $this->httpRequestAssert('/twill/media-library/medias?page=1&type=image', 'GET', [], 200);

        // User can upload medias
        $this->httpRequestAssert('/twill/media-library/medias', 'POST', [], 200);

        // User can access settings
        $this->httpRequestAssert('/twill/settings/seo', 'GET', [], 200);

        // User can access users list
        $this->httpRequestAssert("/twill/users", 'GET', [], 200);

        // User can access other profiles
        $this->httpRequestAssert("/twill/users/{$guest->id}/edit", 'GET', [], 200);
        $this->httpRequestAssert("/twill/users/{$publisher->id}/edit", 'GET', [], 200);

        // User can access own profile
        $this->httpRequestAssert("/twill/users/{$admin->id}/edit", 'GET', [], 200);


        $author = $this->createAuthor();

        // User can access authors list
        $this->httpRequestAssert("/twill/personnel/authors", 'GET', [], 200);

        // User can access author details
        $this->httpRequestAssert("/twill/personnel/authors/{$author->id}/edit", 'GET', [], 200);

        // User can create authors
        $this->httpRequestAssert('/twill/personnel/authors', 'POST', [], 200);
    }
}
