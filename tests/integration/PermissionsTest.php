<?php

namespace A17\Twill\Tests\Integration;

use A17\Twill\Enums\PermissionLevel;
use A17\Twill\Models\Group;
use A17\Twill\Models\Role;
use A17\Twill\Models\User;
use A17\Twill\PermissionAuthServiceProvider;
use App\Repositories\PostingRepository;
use Illuminate\Support\Facades\DB;

class PermissionsTest extends PermissionsTestBase
{
    protected function getPackageProviders($app)
    {
        // This config must be set before loading TwillServiceProvider to select
        // between AuthServiceProvider and PermissionAuthServiceProvider
        $app['config']->set('twill.enabled.permissions-management', true);

        PermissionAuthServiceProvider::disableCache();

        return parent::getPackageProviders($app);
    }

    public function createUser($role, $group = null)
    {
        $user = $this->makeUser();
        $user->role_id = $role->id;
        $user->save();

        if ($group) {
            $group->users()->attach($user->id);
        }

        return $user;
    }

    public function createRole($roleName)
    {
        return Role::create([
            'name' => $roleName,
            'published' => true,
            'in_everyone_group' => true,
        ]);
    }

    public function createGroup($groupName)
    {
        return Group::create([
            'name' => $groupName,
            'published' => true,
        ]);
    }

    public function withGlobalPermission($target, $permissionName, $callback)
    {
        $target->grantGlobalPermission($permissionName);

        $callback();

        $target->revokeGlobalPermission($permissionName);
    }

    public function withModulePermission($target, $module, $permissionName, $callback)
    {
        $model = getModelByModuleName($module);

        $target->grantModulePermission($permissionName, $model);

        $callback();

        $target->revokeModulePermission($permissionName, $model);
    }

    public function withItemPermission($target, $item, $permissionName, $callback)
    {
        $target->grantModuleItemPermission($permissionName, $item);

        $callback();

        $target->revokeModuleItemPermission($permissionName, $item);
    }

    // FIXME — this is needed for the new admin routes to take effect in the next test,
    // because files are copied in `setUp()` after the app is initialized.
    public function testDummy()
    {
        $this->assertTrue(true);
    }

    public function testRolePermissions()
    {
        app('config')->set('twill.permissions.level', PermissionLevel::LEVEL_ROLE);

        $role = $this->createRole('Tester');
        $user = $this->createUser($role);

        $tempRole = $this->createRole('Temporary');
        $tempUser = $this->createUser($tempRole);

        // User is logged in
        $this->loginUser($user);

        // User can access settings if permitted
        $this->httpRequestAssert('/twill/settings/seo', 'GET', [], 403);
        $this->withGlobalPermission($role, 'edit-settings', function () {
            $this->httpRequestAssert('/twill/settings/seo', 'GET', [], 200);
        });

        // User can access media library & files if permitted
        $this->httpRequestAssert('/twill/media-library/medias?page=1&type=image', 'GET', [], 403);
        $this->httpRequestAssert('/twill/file-library/files?page=1', 'GET', [], 403);
        $this->withGlobalPermission($role, 'access-media-library', function () {
            $this->httpRequestAssert('/twill/media-library/medias?page=1&type=image', 'GET', [], 200);
            $this->httpRequestAssert('/twill/file-library/files?page=1', 'GET', [], 200);
        });

        // User can edit media library & files if permitted
        $this->httpRequestAssert('/twill/media-library/medias', 'POST', [], 403);
        $this->httpRequestAssert('/twill/file-library/files', 'POST', [], 403);
        $this->withGlobalPermission($role, 'edit-media-library', function () {
            $this->httpRequestAssert('/twill/media-library/medias', 'POST', [], 200);
            $this->httpRequestAssert('/twill/file-library/files', 'POST', [], 200);
        });

        // User can access own profile
        $this->httpRequestAssert("/twill/users/{$user->id}/edit", 'GET', [], 200);

        // User can't access other profiles
        $this->httpRequestAssert("/twill/users/{$tempUser->id}/edit", 'GET', [], 403);

        // User can edit users if permitted
        $this->httpRequestAssert('/twill/users', 'GET', [], 403);
        $this->withGlobalPermission($role, 'edit-users', function () use ($tempUser) {
            $this->httpRequestAssert('/twill/users', 'GET', [], 200);
            $this->httpRequestAssert("/twill/users/{$tempUser->id}/edit", 'GET', [], 200);
        });

        // User can edit roles if permitted
        $this->httpRequestAssert('/twill/roles', 'GET', [], 403);
        $this->withGlobalPermission($role, 'edit-user-roles', function () use ($tempRole) {
            $this->httpRequestAssert('/twill/roles', 'GET', [], 200);
            $this->httpRequestAssert("/twill/roles/{$tempRole->id}/edit", 'GET', [], 200);
        });

        // User can't access groups list (feature not enabled with `twill.permissions.level` === \A17\Twill\Enums\PermissionLevel::LEVEL_ROLE)
        $this->httpRequestAssert('/twill/groups', 'GET', [], 403);

        $posting = $this->createPosting();

        // User can access items list if permitted
        $this->httpRequestAssert('/twill/postings', 'GET', [], 403);
        $this->withModulePermission($role, 'postings', 'view-module', function () {
            $this->httpRequestAssert('/twill/postings', 'GET', [], 200);
        });

        // User can edit item if permitted
        $this->httpRequestAssert("/twill/postings/{$posting->id}/edit", 'GET', [], 403);
        $this->withModulePermission($role, 'postings', 'edit-module', function () use ($posting) {
            $this->httpRequestAssert("/twill/postings/{$posting->id}/edit", 'GET', [], 200);
        });

        // User can create item if permitted
        $this->httpRequestAssert('/twill/postings', 'POST', [], 403);
        $this->withModulePermission($role, 'postings', 'edit-module', function () {
            $this->httpRequestAssert('/twill/postings', 'POST', [], 200);
        });

        // User can manage modules if permitted
        $this->httpRequestAssert("/twill/postings/{$posting->id}/edit", 'GET', [], 403);
        $this->httpRequestAssert('/twill/postings', 'POST', [], 403);
        $this->withGlobalPermission($role, 'manage-modules', function () use ($posting) {
            $this->httpRequestAssert("/twill/postings/{$posting->id}/edit", 'GET', [], 200);
            $this->httpRequestAssert('/twill/postings', 'POST', [], 200);
        });
    }

    public function testGroupPermissions()
    {
        app('config')->set('twill.permissions.level', PermissionLevel::LEVEL_ROLE_GROUP);

        $role = $this->createRole('Tester');
        $group = $this->createGroup('Beta');
        $user = $this->createUser($role, $group);

        // User is logged in
        $this->loginUser($user);

        // User role can edit groups if permitted
        $this->httpRequestAssert('/twill/groups', 'GET', [], 403);
        $this->httpRequestAssert("/twill/groups/{$group->id}/edit", 'GET', [], 403);
        $this->withGlobalPermission($role, 'edit-user-groups', function () use ($group) {
            $this->httpRequestAssert('/twill/groups', 'GET', [], 200);
            $this->httpRequestAssert("/twill/groups/{$group->id}/edit", 'GET', [], 200);
        });

        // User role can't edit Everyone group
        $this->withGlobalPermission($role, 'edit-user-groups', function () {
            $everyoneGroup = Group::getEveryoneGroup();
            $this->httpRequestAssert("/twill/groups/{$everyoneGroup->id}/edit", 'GET', [], 403);
        });

        $posting = $this->createPosting();

        // User group can access items list if permitted
        $this->httpRequestAssert('/twill/postings', 'GET', [], 403);
        $this->withModulePermission($group, 'postings', 'view-module', function () {
            $this->httpRequestAssert('/twill/postings', 'GET', [], 200);
        });

        // User group can edit item if permitted
        $this->httpRequestAssert("/twill/postings/{$posting->id}/edit", 'GET', [], 403);
        $this->withModulePermission($group, 'postings', 'edit-module', function () use ($posting) {
            $this->httpRequestAssert("/twill/postings/{$posting->id}/edit", 'GET', [], 200);
        });

        // User group can create item if permitted
        $this->httpRequestAssert('/twill/postings', 'POST', [], 403);
        $this->withModulePermission($group, 'postings', 'edit-module', function () {
            $this->httpRequestAssert('/twill/postings', 'POST', [], 200);
        });
    }

    public function testModulePermissions()
    {
        app('config')->set('twill.permissions.level', PermissionLevel::LEVEL_ROLE_GROUP_ITEM);

        $role = $this->createRole('Tester');
        $group = $this->createGroup('Beta');
        $user = $this->createUser($role, $group);

        // User is logged in
        $this->loginUser($user);

        $posting = $this->createPosting();

        // User role can manage module if permitted
        $this->httpRequestAssert("/twill/postings/{$posting->id}/edit", 'GET', [], 403);
        $this->httpRequestAssert('/twill/postings', 'POST', [], 403);
        $this->withModulePermission($role, 'postings', 'manage-module', function () use ($posting) {
            $this->httpRequestAssert("/twill/postings/{$posting->id}/edit", 'GET', [], 200);
            $this->httpRequestAssert('/twill/postings', 'POST', [], 200);
        });

        // User group can access items list if permitted
        $this->httpRequestAssert('/twill/postings', 'GET', [], 403);
        $this->withItemPermission($group, $posting, 'view-item', function () {
            $this->httpRequestAssert('/twill/postings', 'GET', [], 200);
            $this->assertDontSee('a17-singleselect-permissions');
        });

        // User group can edit item if permitted
        $this->httpRequestAssert("/twill/postings/{$posting->id}/edit", 'GET', [], 403);
        $this->withItemPermission($group, $posting, 'edit-item', function () use ($posting) {
            $this->httpRequestAssert("/twill/postings/{$posting->id}/edit", 'GET', [], 200);
            $this->assertDontSee('a17-singleselect-permissions');
        });

        // User group can manage item if permitted
        $this->httpRequestAssert("/twill/postings/{$posting->id}/edit", 'GET', [], 403);
        $this->withItemPermission($group, $posting, 'manage-item', function () use ($posting) {
            $this->httpRequestAssert("/twill/postings/{$posting->id}/edit", 'GET', [], 200);
            $this->assertSee('a17-singleselect-permissions');
        });
    }

    public function testUserModulePermissions()
    {
        app('config')->set('twill.permissions.level', PermissionLevel::LEVEL_ROLE_GROUP_ITEM);

        $role = $this->createRole('Tester');
        $user = $this->createUser($role);

        // User is logged in
        $this->loginUser($user);

        $posting = $this->createPosting();

        // User can access items list if permitted
        $this->httpRequestAssert('/twill/postings', 'GET', [], 403);
        $this->withItemPermission($user, $posting, 'view-item', function () {
            $this->httpRequestAssert('/twill/postings', 'GET', [], 200);
            $this->assertDontSee('a17-singleselect-permissions');
        });

        // User can edit item if permitted
        $this->httpRequestAssert("/twill/postings/{$posting->id}/edit", 'GET', [], 403);
        $this->withItemPermission($user, $posting, 'edit-item', function () use ($posting) {
            $this->httpRequestAssert("/twill/postings/{$posting->id}/edit", 'GET', [], 200);
            $this->assertDontSee('a17-singleselect-permissions');
        });

        // User can manage item if permitted
        $this->httpRequestAssert("/twill/postings/{$posting->id}/edit", 'GET', [], 403);
        $this->withItemPermission($user, $posting, 'manage-item', function () use ($posting) {
            $this->httpRequestAssert("/twill/postings/{$posting->id}/edit", 'GET', [], 200);
            $this->assertSee('a17-singleselect-permissions');
        });
    }

    public function testEveryoneGroup()
    {
        app('config')->set('twill.permissions.level', PermissionLevel::LEVEL_ROLE_GROUP_ITEM);

        $everyoneGroup = Group::getEveryoneGroup();

        $userRole = $this->createRole('Tester');
        $userRole->grantGlobalPermission('edit-users');
        $userRole->grantGlobalPermission('edit-user-roles');
        $user = $this->createUser($userRole);

        // User is logged in
        $this->loginUser($user);

        // Everyone group is empty
        $everyoneGroup->users()->detach();
        $this->assertEquals(0, $everyoneGroup->users()->count());

        $newRole = $this->createRole('New Role');

        // A new user with a role in Everyone group is automatically added to the group
        $this->httpRequestAssert('/twill/users', 'POST', [
            'name' => 'Bob',
            'email' => 'bob@test.test',
            'role_id' => $newRole->id,
            'published' => true,
        ], 200);
        $this->assertEquals(1, $everyoneGroup->users()->count());

        // Removing a role from Everyone group removes all users from the group
        $this->httpRequestAssert("/twill/roles/{$newRole->id}", 'PUT', [
            'name' => 'Tester',
            'published' => true,
            'in_everyone_group' => false,
        ], 200);
        $this->assertEquals(0, $everyoneGroup->users()->count());

        // Adding a role in Everyone group adds all users to the group
        $this->httpRequestAssert("/twill/roles/{$newRole->id}", 'PUT', [
            'name' => 'Tester',
            'published' => true,
            'in_everyone_group' => true,
        ], 200);
        $this->assertEquals(1, $everyoneGroup->users()->count());
    }

    public function testRoleBasedAccessLevel()
    {
        app('config')->set('twill.permissions.level', PermissionLevel::LEVEL_ROLE_GROUP_ITEM);

        User::query()->forceDelete();
        Role::query()->forceDelete();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        Role::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        [$role1, $role2, $role3] = collect([1, 2, 3])->map(function ($i) {
            $role = $this->createRole("Role $i");
            $role->position = $i;
            $role->save();

            $role->grantGlobalPermission('edit-users');
            $role->grantGlobalPermission('edit-user-roles');

            return $role;
        })->all();

        $userRole1 = $this->createUser($role1);
        $userRole2_A = $this->createUser($role2);
        $userRole2_B = $this->createUser($role2);
        $userRole3 = $this->createUser($role3);

        $this->assertEquals(3, Role::count());
        $this->assertEquals(4, User::count());

        // User is logged in
        $this->loginUser($userRole2_A);

        // User can't edit higher level roles
        $this->httpRequestAssert("/twill/roles/{$role1->id}/edit", 'GET', [], 403);

        // User can edit equal or lower level roles
        $this->httpRequestAssert("/twill/roles/{$role2->id}/edit", 'GET', [], 200);
        $this->httpRequestAssert("/twill/roles/{$role3->id}/edit", 'GET', [], 200);

        // User can't edit higher level users
        $this->httpRequestAssert("/twill/users/{$userRole1->id}/edit", 'GET', [], 403);

        // User can edit equal or lower level users
        $this->httpRequestAssert("/twill/users/{$userRole2_B->id}/edit", 'GET', [], 200);
        $this->httpRequestAssert("/twill/users/{$userRole3->id}/edit", 'GET', [], 200);

        // User can't assign higher level roles
        $this->httpRequestAssert('/twill/users', 'POST', [
            'name' => 'Test',
            'email' => 'test@test.test',
            'role_id' => $role1->id,
            'published' => true,
        ]);
        $this->assertSee('The selected role id is invalid');
        $this->assertEquals(4, User::count());

        // User can assign equal level roles
        $this->httpRequestAssert('/twill/users', 'POST', [
            'name' => 'Test',
            'email' => 'test@test.test',
            'role_id' => $role2->id,
            'published' => true,
        ]);
        $this->assertDontSee('The selected role id is invalid');
        $this->assertEquals(5, User::count());

        // User can assign lower level roles
        $this->httpRequestAssert('/twill/users', 'POST', [
            'name' => 'Test2',
            'email' => 'test2@test.test',
            'role_id' => $role3->id,
            'published' => true,
        ]);
        $this->assertDontSee('The selected role id is invalid');
        $this->assertEquals(6, User::count());
    }

    public function testHandlePermissionsGetFormFields()
    {
        app('config')->set('twill.permissions.level', PermissionLevel::LEVEL_ROLE_GROUP_ITEM);

        User::query()->forceDelete();
        Role::query()->forceDelete();
        Group::query()->forceDelete();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        Role::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $managerRole = $this->createRole('Manager');
        $managerRole->grantGlobalPermission('manage-modules');
        $manager = $this->createUser($managerRole);

        $viewerRole = $this->createRole('Viewer');
        $viewer = $this->createUser($viewerRole);
        $alpha = $this->createGroup('Alpha');
        $beta = $this->createGroup('Beta');
        $viewer->groups()->attach($alpha);
        $viewer->groups()->attach($beta);

        $this->assertEquals(2, User::count());
        $this->assertEquals(2, Role::count());
        $this->assertEquals(2, Group::count());
        $this->assertEquals(2, $viewer->groups->count());

        $this->loginUser($manager);

        $posting = $this->createPosting();

        // Manager has highest permission
        $fields = app(PostingRepository::class)->getFormFields($posting);
        $this->assertEquals('manage-item', $fields['user_1_permission']);

        // Viewer has no permission
        $this->assertEquals('', $fields['user_2_permission']);

        $viewer->grantModuleItemPermission('view-item', $posting);

        // Viewer has view-item permission
        $fields = app(PostingRepository::class)->getFormFields($posting);
        $this->assertEquals('view-item', $fields['user_2_permission']);

        $alpha->grantModuleItemPermission('edit-item', $posting);

        // Viewer has edit-item permission through group
        $fields = app(PostingRepository::class)->getFormFields($posting);
        $this->assertEquals('edit-item', $fields['user_2_permission']);

        $beta->grantModuleItemPermission('manage-item', $posting);

        // Viewer has manage-item permission through other group
        $fields = app(PostingRepository::class)->getFormFields($posting);
        $this->assertEquals('manage-item', $fields['user_2_permission']);

        $viewer->groups()->detach($beta);
        $viewer->role->grantModulePermission('manage-module', 'App\\Models\\Posting');

        // Viewer has manage-item permission through role
        $fields = app(PostingRepository::class)->getFormFields($posting);
        $this->assertEquals('manage-item', $fields['user_2_permission']);
    }

    public function testHandlePermissionsAfterSave()
    {
        app('config')->set('twill.permissions.level', PermissionLevel::LEVEL_ROLE_GROUP_ITEM);

        User::query()->forceDelete();
        Role::query()->forceDelete();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        Role::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $managerRole = $this->createRole('Manager');
        $managerRole->grantGlobalPermission('manage-modules');
        $manager = $this->createUser($managerRole);

        $viewerRole = $this->createRole('Viewer');
        $viewerRole->grantModulePermission('view-module', 'App\\Models\\Posting');
        $viewer = $this->createUser($viewerRole);

        $this->assertEquals(2, User::count());
        $this->assertEquals(2, Role::count());

        $this->loginUser($manager);

        $posting = $this->createPosting();

        // Viewer has no direct view-item permission
        $this->assertEquals(0, $viewer->permissions()->ofItem($posting)->count());

        // Viewer has view-item permission through role
        $fields = app(PostingRepository::class)->getFormFields($posting);
        $this->assertEquals('view-item', $fields['user_2_permission']);

        app(PostingRepository::class)->update($posting->id, array_merge($fields, [
            'user_2_permission' => 'view-item',
        ]));

        // Viewer has no direct view-item permission — nothing changed
        $this->assertEquals(0, $viewer->permissions()->ofItem($posting)->count());

        app(PostingRepository::class)->update($posting->id, array_merge($fields, [
            'user_2_permission' => 'edit-item',
        ]));

        // Viewer has direct edit-item permission
        $this->assertEquals(1, $viewer->permissions()->ofItem($posting)->count());
        $this->assertEquals('edit-item', $viewer->permissions()->ofItem($posting)->first()->name);
    }
}
