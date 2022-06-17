<?php

namespace A17\Twill\Tests\Integration;

use App\Twill\Capsules\Posts\PostsCapsuleServiceProvider;

class CapsuleAutoloadTest extends TestCase
{
    protected $allFiles = [
        '{$stubs}/capsules/posts/database/migrations/2020_08_14_205624_create_posts_tables.php' =>
            '{$app}/Twill/Capsules/Posts/database/migrations/2020_08_14_205624_create_posts_tables.php',

        '{$stubs}/capsules/posts/Http/Requests/PostRequest.php' =>
            '{$app}/Twill/Capsules/Posts/Http/Requests/PostRequest.php',

        '{$stubs}/capsules/posts/Http/Controllers/PostController.php' =>
            '{$app}/Twill/Capsules/Posts/Http/Controllers/PostController.php',

        '{$stubs}/capsules/posts/Repositories/PostRepository.php' =>
            '{$app}/Twill/Capsules/Posts/Repositories/PostRepository.php',

        '{$stubs}/capsules/posts/Models/PostTranslation.php' =>
            '{$app}/Twill/Capsules/Posts/Models/PostTranslation.php',

        '{$stubs}/capsules/posts/Models/PostSlug.php' =>
            '{$app}/Twill/Capsules/Posts/Models/PostSlug.php',

        '{$stubs}/capsules/posts/Models/Post.php' =>
            '{$app}/Twill/Capsules/Posts/Models/Post.php',

        '{$stubs}/capsules/posts/Models/PostRevision.php' =>
            '{$app}/Twill/Capsules/Posts/Models/PostRevision.php',

        '{$stubs}/capsules/posts/resources/views/admin/form.blade.php' =>
            '{$app}/Twill/Capsules/Posts/resources/views/admin/form.blade.php',

        '{$stubs}/capsules/posts/resources/views/admin/create.blade.php' =>
            '{$app}/Twill/Capsules/Posts/resources/views/admin/create.blade.php',

        '{$stubs}/capsules/posts/routes/admin.php' =>
            '{$app}/Twill/Capsules/Posts/routes/admin.php',

        '{$stubs}/capsules/posts/PostsCapsuleServiceProvider.php' =>
            '{$app}/Twill/Capsules/Posts/PostsCapsuleServiceProvider.php',
    ];

    public function getPackageProviders($app)
    {
        config()->set([
            'twill.capsules.list' => [
                [
                    'name' => 'Posts',
                    'enabled' => true,
                ],
            ],
        ]);

        return parent::getPackageProviders($app);
    }

    public function testStubCapsuleIsLoaded(): void
    {
        class_exists(
            "App\Twill\Capsules\Posts\Models\Post"
        );
    }

    public function testServiceProviderIsBooted(): void
    {
        $this->assertTrue(PostsCapsuleServiceProvider::$isBooted);
    }
}
