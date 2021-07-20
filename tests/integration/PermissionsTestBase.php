<?php

namespace A17\Twill\Tests\Integration;

use App\Repositories\PostRepository;
use A17\Twill\Models\User;
use Illuminate\Support\Facades\Hash;

abstract class PermissionsTestBase extends TestCase
{
    protected $allFiles = [
        // posts module
        '{$stubs}/permissions/posts/2021_07_20_132405_create_posts_tables.php' => '{$database}/migrations/',
        '{$stubs}/permissions/posts/Post.php' => '{$app}/Models/',
        '{$stubs}/permissions/posts/PostController.php' => '{$app}/Http/Controllers/Admin/',
        '{$stubs}/permissions/posts/PostRepository.php' => '{$app}/Repositories/',
        '{$stubs}/permissions/posts/PostRequest.php' => '{$app}/Http/Requests/Admin/',
        '{$stubs}/permissions/posts/form.blade.php' => '{$resources}/views/admin/posts/',

        // general
        '{$stubs}/permissions/settings/seo.blade.php' => '{$resources}/views/admin/settings/',
        '{$stubs}/permissions/admin.php' => '{$base}/routes/admin.php',
        '{$stubs}/permissions/translatable.php' => '{$config}/',
        '{$stubs}/permissions/twill-navigation.php' => '{$config}/',
        '{$stubs}/permissions/twill.php' => '{$config}/',
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->copyFiles($this->allFiles);

        $this->migrate();
    }

    public function loginUser($user)
    {
        $this->loginAs($user->email, $user->email);
    }

    public function makeUser()
    {
        $user = User::make([
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'published' => true,
        ]);

        $user->password = Hash::make($user->email);

        return $user;
    }

    public function createPost()
    {
        return app(PostRepository::class)->create([
            'title' => $this->faker->name,
            'published' => true,
        ]);
    }
}
