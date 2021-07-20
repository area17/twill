<?php

namespace A17\Twill\Tests\Integration;

use A17\Twill\Models\Role;
use A17\Twill\PermissionAuthServiceProvider;

class PermissionsTest extends PermissionsTestBase
{
    protected function getPackageProviders($app)
    {
        PermissionAuthServiceProvider::disableCache();

        // This config must be set before loading TwillServiceProvider to select
        // between AuthServiceProvider and PermissionAuthServiceProvider
        $app['config']->set('twill.enabled.permissions-management', true);
        $app['config']->set('twill.permissions.modules', ['posts']);

        return parent::getPackageProviders($app);
    }

    public function createUser($role)
    {
        $user = $this->makeUser();
        $user->role_id = $role->id;
        $user->save();

        return $user;
    }

    public function createRole($roleName)
    {
        $role = Role::create([
            'name' => $roleName,
            'published' => true,
            'in_everyone_group' => true,
        ]);

        return $role;
    }

    public function attachRolePermission($role, $permissionName)
    {
        $role->grantGlobalPermission($permissionName);
    }

    public function attachRoleModulePermission($role, $module, $permissionName)
    {
        $role->grantModulePermission($permissionName, getModelByModuleName($module));
    }

    public function testRolePermissions()
    {
        $tempRole = $this->createRole('Temporary');
        $tempUser = $this->createUser($tempRole);

        $role = $this->createRole('Tester');
        $user = $this->createUser($role);

        // User is logged in
        $this->loginUser($user);
        $this->httpRequestAssert('/twill');
        $this->assertSee($user->name);

        // User can access settings if permitted
        $this->httpRequestAssert('/twill/settings/seo', 'GET', [], 403);
        $this->attachRolePermission($role, 'edit-settings');
        $this->httpRequestAssert('/twill/settings/seo', 'GET', [], 200);

        // User can access media library & files if permitted
        $this->httpRequestAssert('/twill/media-library/medias?page=1&type=image', 'GET', [], 403);
        $this->httpRequestAssert('/twill/file-library/files?page=1', 'GET', [], 403);
        $this->attachRolePermission($role, 'access-media-library');
        $this->httpRequestAssert('/twill/media-library/medias?page=1&type=image', 'GET', [], 200);
        $this->httpRequestAssert('/twill/file-library/files?page=1', 'GET', [], 200);

        // User can edit media library & files if permitted
        $this->httpRequestAssert('/twill/media-library/medias', 'POST', [], 403);
        $this->httpRequestAssert('/twill/file-library/files', 'POST', [], 403);
        $this->attachRolePermission($role, 'edit-media-library');
        $this->httpRequestAssert('/twill/media-library/medias', 'POST', [], 200);
        $this->httpRequestAssert('/twill/file-library/files', 'POST', [], 200);

        // User can access own profile
        $this->httpRequestAssert("/twill/users/{$user->id}/edit", 'GET', [], 200);

        // User can't access other profiles
        $this->httpRequestAssert("/twill/users/{$tempUser->id}/edit", 'GET', [], 403);

        // User can access users list if permitted
        $this->httpRequestAssert("/twill/users", 'GET', [], 403);
        $this->attachRolePermission($role, 'edit-users');
        $this->httpRequestAssert("/twill/users", 'GET', [], 200);

        // User can edit other profiles if permitted
        $this->httpRequestAssert("/twill/users/{$tempUser->id}/edit", 'GET', [], 200);

        // User can access roles list if permitted
        $this->httpRequestAssert("/twill/roles", 'GET', [], 403);
        $this->attachRolePermission($role, 'edit-user-role');
        $this->httpRequestAssert("/twill/roles", 'GET', [], 200);

        // User can't access groups list (feature is not enabled)
        $this->httpRequestAssert("/twill/groups", 'GET', [], 403);


        $post = $this->createPost();

        // User can access items list if permitted
        $this->httpRequestAssert("/twill/posts", 'GET', [], 403);
        $this->attachRoleModulePermission($role, 'posts', 'view-module');
        $this->httpRequestAssert("/twill/posts", 'GET', [], 200);

        // User can access item details if permitted
        $this->httpRequestAssert("/twill/posts/{$post->id}/edit", 'GET', [], 403);
        $this->attachRoleModulePermission($role, 'posts', 'edit-module');
        $this->httpRequestAssert("/twill/posts/{$post->id}/edit", 'GET', [], 200);
    }
}
