<?php

namespace A17\Twill\Tests\Integration;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use A17\Twill\Services\Routing\HasRoutes;
use A17\Twill\Services\Capsules\HasCapsules;
use Illuminate\Routing\Router;

class CapsulesTest extends TestCase
{
    use HasCapsules, HasRoutes;

    protected $capsules = [
        'posts',
        'artists',
        'planes',
        'cars',
        'tables',
        'chairs',
        'ventilators',
        'houses',
        'computers',
    ];

    protected $capsuleName;
    protected $capsuleNameSingular;
    protected $capsuleModel;
    protected $capsuleClassName;
    protected $capsuleModelName;
    protected $manager;

    public function setUp(): void
    {
        $this->selectCapsule();

        parent::setUp();

        $this->manager = app('twill.capsules.manager');

        $this->login();

        app()->setLocale('en');

        $this->makeCapsule($this->capsuleName);
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
                $this->capsuleName => [
                    'title' => Str::studly($this->capsuleClassName),

                    'module' => true,
                ],
            ],
        ]);
    }

    public function getPackageProviders($app)
    {
        $capsules = collect($this->capsules)->map(function ($capsule) {
            return ['name' => Str::studly($capsule), 'enabled' => true];
        });

        config()->set([
            'twill.capsules.list' => $capsules,
        ]);

        return parent::getPackageProviders($app);
    }

    /**
     * @group capsule
     */
    public function testCapsuleProviderWasRegistered()
    {
        class_exists(
            "App\Twill\Capsules\{$this->capsuleClassName}\Models\{$this->capsuleModelName}"
        );

        class_exists('A17\Twill\Services\Modules\HasModules');
    }

    /**
     * @group capsule
     */
    public function testCanMigrateDatabase()
    {
        $this->assertTrue(Schema::hasTable($this->capsuleName));
        $this->assertTrue(
            Schema::hasTable("{$this->capsuleNameSingular}_translations")
        );
        $this->assertTrue(
            Schema::hasTable("{$this->capsuleNameSingular}_slugs")
        );
        $this->assertTrue(
            Schema::hasTable("{$this->capsuleNameSingular}_revisions")
        );
    }

    /**
     * @group capsule
     */
    public function testCanBootRoutes()
    {
        $routes = [
            "twill/{$this->capsuleName}/reorder",
            "twill/{$this->capsuleName}/publish",
            "twill/{$this->capsuleName}/bulkPublish",
            "twill/{$this->capsuleName}/browser",
            "twill/{$this->capsuleName}/feature",
            "twill/{$this->capsuleName}/bulkFeature",
            "twill/{$this->capsuleName}/tags",
            "twill/{$this->capsuleName}/preview/{id}",
            "twill/{$this->capsuleName}/restore",
            "twill/{$this->capsuleName}/bulkRestore",
            "twill/{$this->capsuleName}/forceDelete",
            "twill/{$this->capsuleName}/bulkForceDelete",
            "twill/{$this->capsuleName}/bulkDelete",
            "twill/{$this->capsuleName}/restoreRevision/{id}",
            "twill/{$this->capsuleName}/duplicate/{id}",
            "twill/{$this->capsuleName}",
            "twill/{$this->capsuleName}/create",
            "twill/{$this->capsuleName}",
            "twill/{$this->capsuleName}/{{$this->capsuleNameSingular}}",
            "twill/{$this->capsuleName}/{{$this->capsuleNameSingular}}/edit",
            "twill/{$this->capsuleName}/{{$this->capsuleNameSingular}}",
            "twill/{$this->capsuleName}/{{$this->capsuleNameSingular}}",
        ];

        collect($routes)->each(function ($uri) {
            $this->assertContains($uri, $this->getAllUris());
        });
    }

    /**
     * @group capsule
     */
    public function testCanDisplayDashboard()
    {
        $this->request('/twill')->assertStatus(200);

        $this->assertSee($this->capsuleClassName);

        $this->request("/twill/{$this->capsuleName}")->assertStatus(200);

        $this->assertSee('All items');

        $this->assertSee('Title');

        $this->assertSee('Language');
    }

    protected function createCapsuleModel($count = 1)
    {
        $class = $this->capsuleModel;

        $this->assertEquals(0, $class::count());

        foreach (range(1, $count) as $c) {
            $this->request(
                "/twill/{$this->capsuleName}",
                'POST',
                $data = $this->getCreateAuthorData()
            )->assertStatus(200);
        }

        $firstModel = $class::first();

        $this->assertEquals($count, $class::count());

        $this->assertEquals($firstModel->title, $data['title']['en']);

        return $firstModel;
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

    public function testCreateCapsuleModel()
    {
        $this->createCapsuleModel();
    }

    public function testCanSeeModelInListing()
    {
        $model = $this->createCapsuleModel();

        $this->request("/twill/{$this->capsuleName}")->assertStatus(200);

        $this->assertSee('Title');

        $this->assertSee($model->title);
    }

    public function testCanPublishModel()
    {
        $model = $this->createCapsuleModel();

        $this->assertEquals('0', $model->published);

        $this->request("/twill/{$this->capsuleName}/publish", 'PUT', [
            'id' => $model->id,
            'active' => false,
        ])->assertStatus(200);

        $this->assertNothingWrongHappened();

        $model->refresh();

        $this->assertEquals('1', $model->published);
    }

    public function makeCapsule()
    {
        $this->artisan("twill:make:capsule {$this->capsuleName} --all --force")
            ->expectsConfirmation('Do you also want to generate the preview file?', 'no');

        $this->registerCapsuleRoutes(
            app(Router::class),
            $this->getCapsuleByModule($this->capsuleName),
            $this->manager
        );

        $this->migrate();
    }

    public function selectCapsule()
    {
        foreach ($this->capsules as $capsule) {
            $class = Str::studly($capsule);

            $class = "Create{$class}Tables";

            if (!collect(get_declared_classes())->contains($class)) {
                $this->capsuleName = $capsule;

                break;
            }
        }

        $this->capsuleClassName = Str::studly($this->capsuleName);

        $this->capsuleNameSingular = Str::singular($this->capsuleName);

        $this->capsuleModelName = Str::singular($this->capsuleClassName);

        $this->capsuleModel = "App\Twill\Capsules\\{$this->capsuleClassName}\\Models\\{$this->capsuleModelName}";
    }
}
