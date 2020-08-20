<?php

namespace A17\Twill\Tests\Integration;

use App\Models\Author;
use Illuminate\Support\Str;
use A17\Twill\AuthServiceProvider;
use A17\Twill\TwillServiceProvider;
use A17\Twill\RouteServiceProvider;
use App\Models\Revisions\AuthorRevision;
use Illuminate\Support\Facades\Schema;
use A17\Twill\ValidationServiceProvider;
use App\Twill\Capsules\Posts\Data\Models\Post;
use App\Twill\Capsules\Posts\Data\Models\PostTranslation;

class CapsulesTest extends TestCase
{
    protected $allFiles = [
        '{$stubs}/capsules/posts/database/migrations/2020_08_14_205624_create_posts_tables.php' =>
            '{$app}/Twill/Capsules/Posts/database/migrations/2020_08_14_205624_create_posts_tables.php',

        '{$stubs}/capsules/posts/app/Http/Requests/PostRequest.php' =>
            '{$app}/Twill/Capsules/Posts/app/Http/Requests/PostRequest.php',

        '{$stubs}/capsules/posts/app/Http/Controllers/PostController.php' =>
            '{$app}/Twill/Capsules/Posts/app/Http/Controllers/PostController.php',

        '{$stubs}/capsules/posts/app/Data/Repositories/PostRepository.php' =>
            '{$app}/Twill/Capsules/Posts/app/Data/Repositories/PostRepository.php',

        '{$stubs}/capsules/posts/app/Data/Models/PostTranslation.php' =>
            '{$app}/Twill/Capsules/Posts/app/Data/Models/PostTranslation.php',

        '{$stubs}/capsules/posts/app/Data/Models/PostSlug.php' =>
            '{$app}/Twill/Capsules/Posts/app/Data/Models/PostSlug.php',

        '{$stubs}/capsules/posts/app/Data/Models/Post.php' =>
            '{$app}/Twill/Capsules/Posts/app/Data/Models/Post.php',

        '{$stubs}/capsules/posts/app/Data/Models/PostRevision.php' =>
            '{$app}/Twill/Capsules/Posts/app/Data/Models/PostRevision.php',

        '{$stubs}/capsules/posts/resources/views/admin/form.blade.php' =>
            '{$app}/Twill/Capsules/Posts/resources/views/admin/form.blade.php',

        '{$stubs}/capsules/posts/resources/views/admin/create.blade.php' =>
            '{$app}/Twill/Capsules/Posts/resources/views/admin/create.blade.php',

        '{$stubs}/capsules/posts/routes/admin.php' =>
            '{$app}/Twill/Capsules/Posts/routes/admin.php',
    ];

    public function setUp(): void
    {
        $this->freshDatabase();

        parent::setUp();

        $this->login();

        app()->setLocale('en');
    }

    public function loadConfig($file = null)
    {
        config()->set([
            'twill.dashboard' => [
                'modules' => [],
                'analytics' => ['enabled' => false],
                'search_endpoint' => 'admin.search',
            ],
        ]);

        config()->set([
            'twill-navigation' => [
                'posts' => [
                    'title' => 'Posts',

                    'module' => true,
                ],
            ],
        ]);
    }

    public function getPackageProviders($app)
    {
        config()->set([
            'twill.capsules.list' => [['name' => 'Posts', 'enabled' => true]],
        ]);

        app()->instance(
            'autoloader',
            require __DIR__ . '/../../vendor/autoload.php'
        );

        return parent::getPackageProviders($app);
    }

    /**
     * @group capsule
     */
    public function testCapsuleProviderWasRegistered()
    {
        class_exists('App\Twill\Capsules\Posts\Data\Models\Post');

        class_exists('A17\Twill\Services\Modules\HasModules');
    }

    /**
     * @group capsule
     */
    public function testCanMigrateDatabase()
    {
        $this->assertTrue(Schema::hasTable('posts'));
        $this->assertTrue(Schema::hasTable('post_translations'));
        $this->assertTrue(Schema::hasTable('post_slugs'));
        $this->assertTrue(Schema::hasTable('post_revisions'));
    }

    /**
     * @group capsule
     */
    public function testCanDisplayDashboard()
    {
        $this->request('/twill')->assertStatus(200);

        $this->assertSee('Posts');

        $this->request('/twill/posts')->assertStatus(200);

        $this->assertSee('All items');

        $this->assertSee('Title');

        $this->assertSee('Language');
    }

    protected function createPost($count = 1)
    {
        $this->assertEquals(0, Post::count());

        foreach (range(1, $count) as $c) {
            $this->request(
                '/twill/posts',
                'POST',
                $data = $this->getCreateAuthorData()
            )->assertStatus(200);
        }

        $firstPost = Post::first();

        $this->assertEquals($count, Post::count());

        $this->assertEquals($firstPost->title, $data['title']['en']);

        return $firstPost;
    }

    protected function getCreateAuthorData(): array
    {
        $name = $this->name = $this->faker->name;

        return [
            'title' => [
                'en' => ($this->name_en = '[EN] ' . $name),
                'fr' => ($this->name_fr = '[FR] ' . $name),
            ],

            'slug' => [
                'en' => ($this->slug_en = Str::slug($this->name_en)),
                'fr' => ($this->slug_fr = Str::slug($this->name_fr)),
            ],

            'published' => false,
        ];
    }

    public function testCreatePost()
    {
        $this->createPost();
    }

    public function testCanSeePostInListing()
    {
        $post = $this->createPost();

        $this->request('/twill/posts')->assertStatus(200);

        $this->assertSee('Title');

        $this->assertSee($post->title);
    }

    public function testCanPublishPost()
    {
        $post = $this->createPost();

        $this->assertEquals('0', $post->published);

        $this->request('/twill/posts/publish', 'PUT', [
            'id' => $post->id,
            'active' => false,
        ])->assertStatus(200);

        $this->assertNothingWrongHappened();

        $post->refresh();

        $this->assertEquals('1', $post->published);
    }
}
