<?php

namespace A17\Twill\Tests\Integration;

use Carbon\Carbon;
use Faker\Factory as Faker;
use A17\Twill\Models\User;
use Illuminate\Support\Str;
use A17\Twill\AuthServiceProvider;
use A17\Twill\TwillServiceProvider;
use A17\Twill\RouteServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Console\Kernel;
use A17\Twill\ValidationServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    const DATABASE_MEMORY = ':memory:';
    const DEFAULT_PASSWORD = 'secret';
    const DEFAULT_LOCALE = 'en_US';
    const DB_CONNECTION = 'sqlite';

    /**
     * @var \Faker\Generator
     */
    public $faker;

    /**
     * @var \A17\Twill\Tests\Integration\UserClass
     */
    public $superAdmin;

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    public $files;

    /**
     * @var \Carbon\Carbon
     */
    public $now;

    /**
     * @var \Carbon\Carbon
     */
    public $recursiveCounter = 0;

    /**
     * @var \Illuminate\Foundation\Testing\TestResponse
     */
    public $crawler;

    public $paths = [
        '/../resources/views',
        '/../resources/views/admin',
        '/../resources/views/site',
        'Http/Controllers/Admin',
        'Http/Requests/Admin',
        'Models/Revisions',
        'Models/Slugs',
        'Models/Translations',
        'Repositories',
        '/../resources/views/admin/authors',
        '/../resources/views/admin/categories',
        '/../resources/views/site/blocks',
        '/../resources/views/site/layouts',
    ];

    protected function deleteAllTwillPaths(): void
    {
        collect($this->paths)->each(function ($directory) {
            if (file_exists($directory = twill_path($directory))) {
                $this->files->deleteDirectory($directory);
            }
        });
    }

    protected function makeAllTwillPaths(): void
    {
        collect($this->paths)->each(function ($directory) {
            if (!file_exists($directory = twill_path($directory))) {
                $this->files->makeDirectory($directory, 0755, true);
            }
        });
    }

    /**
     * Setup tests.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->instantiateFaker();

        $this->installTwill();
    }

    /**
     * Configure Twill options.
     *
     * @param $app
     */
    public function configTwill($app)
    {
        $app['config']->set('twill.admin_app_url', '');
        $app['config']->set('twill.admin_app_path', 'twill');
        $app['config']->set('twill.auth_login_redirect_path', '/twill');
        $app['config']->set('twill.enabled.users-2fa', true);
        $app['config']->set('twill.enabled.users-image', true);
        $app['config']->set('twill.auth_login_redirect_path', '/twill');
    }

    /**
     * Configure database.
     *
     * @param $app
     */
    protected function configureDatabase($app)
    {
        $app['config']->set(
            'database.default',
            $connection = env('DB_CONNECTION', self::DB_CONNECTION)
        );

        $app['config']->set('activitylog.database_connection', $connection);

        $app['config']->set(
            'database.connections.' . $connection . '.database',
            env('DB_DATABASE', self::DATABASE_MEMORY)
        );
    }

    /**
     * Configure storage path.
     *
     * @param $app
     */
    public function configureStorage($app)
    {
        $app['config']->set(
            'logging.channels.single.path',
            $logFile = __DIR__ . '/../storage/logs/laravel.log'
        );

        if (file_exists($logFile) && is_null(env('TRAVIS_PHP_VERSION'))) {
            unlink($logFile);
        }
    }

    /**
     * Create sqlite database, if needed.
     *
     * @param $database
     */
    protected function createDatabase($database): void
    {
        if ($database !== self::DATABASE_MEMORY) {
            if (file_exists($database)) {
                unlink($database);
            }

            touch($database);
        }
    }

    /**
     * Login the current SuperUser.
     *
     * @return \Illuminate\Foundation\Testing\TestResponse|void
     */
    protected function login()
    {
        $this->request('/twill/login', 'POST', [
            'email' => $this->superAdmin()->email,
            'password' => $this->superAdmin()->unencrypted_password,
        ])->assertStatus(200);

        return $this->crawler;
    }

    /**
     * Boot the TestCase.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function boot($app)
    {
        $this->files = $app->make(Filesystem::class);

        $this->prepareLaravelDirectory();
    }

    /**
     * Fake a super admin.
     */
    public function makeNewSuperAdmin()
    {
        $user = new User();

        $user->setAttribute('name', $this->faker->name);
        $user->setAttribute('email', $this->faker->email);
        $user->setAttribute('password', self::DEFAULT_PASSWORD);
        $user->setAttribute('unencrypted_password', self::DEFAULT_PASSWORD);

        return $this->superAdmin = $user;
    }

    /**
     * Instantiate Faker.
     */
    protected function instantiateFaker(): void
    {
        $this->faker = Faker::create(self::DEFAULT_LOCALE);
    }

    /**
     * Get application package providers.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            AuthServiceProvider::class,
            RouteServiceProvider::class,
            TwillServiceProvider::class,
            ValidationServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $this->freezeTime();

        $this->configureStorage($app);

        $this->configTwill($app);

        $this->configureDatabase($app);

        $this->boot($app);

        $this->setUpDatabase($app);
    }

    /**
     * Setup up the database.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function setUpDatabase($app)
    {
        $connection = $app['config']['database.default'];

        if (
            $driver =
                $app['config'][
                    'database.connections.' . $connection . '.driver'
                ] === self::DB_CONNECTION
        ) {
            $this->createDatabase(
                $app['config'][
                    'database.connections.' . $connection . '.database'
                ]
            );
        }
    }

    /**
     * Our dd.
     *
     * @param $value
     */
    public function dd($value)
    {
        dd($value ?? $this->app[Kernel::class]->output());
    }

    /**
     * Get or make a super admin.
     *
     * @param $force
     * @return \A17\Twill\Models\User|\A17\Twill\Tests\Integration\UserClass
     */
    public function superAdmin($force = false)
    {
        return $this->superAdmin =
            !$this->superAdmin || $force
                ? $this->makeNewSuperAdmin()
                : $this->superAdmin;
    }

    /**
     * Clear and make needed directories in the Laravel directory.
     */
    protected function prepareLaravelDirectory()
    {
        array_map(
            'unlink',
            glob($this->getBasePath() . '/database/migrations/*')
        );

        $this->deleteAllTwillPaths();

        $this->makeAllTwillPaths();
    }

    /**
     * Install Twill.
     */
    public function installTwill()
    {
        $this->artisan('twill:install')
            ->expectsQuestion('Enter an email', $this->superAdmin()->email)
            ->expectsQuestion('Enter a password', $this->superAdmin()->password)
            ->expectsQuestion(
                'Confirm the password',
                $this->superAdmin()->password
            );

        $user = User::where(
            'email',
            $email = $this->superAdmin()->email
        )->first();

        $user->setAttribute(
            'unencrypted_password',
            $this->superAdmin->unencrypted_password
        );

        $this->superAdmin = $user;
    }

    /**
     * Delete a directory.
     *
     * @param string $param
     */
    public function deleteDirectory(string $param)
    {
        if ($this->files->exists($param)) {
            $this->files->deleteDirectory($param);
        }
    }

    /**
     * Get a collection with all routes.
     *
     * @param null $method
     * @return \Illuminate\Support\Collection
     */
    public function getAllRoutes($method = null)
    {
        $routes = Route::getRoutes();

        if ($method) {
            $routes = $routes->get($method);
        }

        return collect($routes)->filter(function ($route) {
            return Str::startsWith($route->action['uses'], 'A17\Twill') ||
                Str::startsWith($route->action['uses'], 'App\\');
        });
    }

    /**
     * Get a collection with all package uris.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllUris()
    {
        return $this->getAllRoutes()
            ->pluck('uri')
            ->sort()
            ->unique()
            ->values();
    }

    /**
     * Get a collection with all package uris.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getUriWithNames()
    {
        return $this->getAllRoutes()->pluck('uri', 'action.as');
    }

    /**
     * Send request to an ajax route.
     *
     * @param $uri
     * @param string $method
     * @param array $parameters
     * @param array $cookies
     * @param array $files
     * @param array $server
     * @param null $content
     * @param bool $followRedirects
     * @param bool $allow500
     *
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    public function request(
        $uri,
        $method = 'GET',
        $parameters = [],
        $cookies = [],
        $files = [],
        $server = [],
        $content = null,
        $followRedirects = true
    ) {
        $request = $followRedirects ? $this->followingRedirects() : $this;

        return $this->crawler = $request->call(
            $method,
            $uri,
            $parameters,
            $cookies,
            $files,
            $server,
            $content
        );
    }

    /**
     * Send request to an ajax route.
     *
     * @param $uri
     * @param string $method
     * @param array $parameters
     * @param array $cookies
     * @param array $files
     * @param array $server
     * @param null $content
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    public function ajax(
        $uri,
        $method = 'GET',
        $parameters = [],
        $cookies = [],
        $files = [],
        $server = [],
        $content = null,
        $followRedirects = false
    ) {
        $server = array_merge($server, [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        return $this->request(
            $uri,
            $method,
            $parameters,
            $cookies,
            $files,
            $server,
            $content,
            $followRedirects
        );
    }

    /**
     * Freeze time.
     */
    public function freezeTime()
    {
        Carbon::setTestNow($this->now = Carbon::now());
    }

    /**
     * Copy all sources to destinations.
     *
     * @param array $files
     */
    public function copyFiles($files)
    {
        collect($files)->each(function ($destination, $source) {
            $this->files->copy(
                $this->makeFileName($source),
                $this->makeFileName($destination, $source)
            );

            usleep(1000 * 100); // 100ms
        });
    }

    /**
     * Replace placeholders to make a filename.
     *
     * @param string $file
     * @param null $source
     * @return mixed
     */
    public function makeFileName($file, $source = null)
    {
        $file = str_replace(
            [
                '{$stubs}',
                '{$database}',
                '{$base}',
                '{$app}',
                '{$resources}',
                '{$config}',
            ],
            [
                stubs(),
                database_path(),
                base_path(),
                app_path(),
                resource_path(),
                config_path(),
            ],
            $file
        );

        if (filled($source) && !Str::endsWith($file, '.php')) {
            $file = $file . basename($source);
        }

        return $file;
    }

    /**
     * Return the contents from current crawler response.
     *
     * @return false|string
     */
    public function content()
    {
        return $this->crawler->getContent();
    }

    /**
     * Assert can see text.
     *
     * @param $text
     */
    public function assertSee($text)
    {
        $this->assertStringContainsString(
            clean_file($text),
            clean_file($this->content())
        );
    }

    /**
     * Assert cannot see text.
     *
     * @param $text
     */
    public function assertDontSee($text)
    {
        $this->assertStringNotContainsString(
            clean_file($text),
            clean_file($this->content())
        );
    }

    /**
     * Skip test if running on Travis
     */
    public function skipOnTravis()
    {
        if (!is_null(env('TRAVIS_PHP_VERSION'))) {
            $this->markTestSkipped('This test cannot be execute on Travis');
        }
    }
}
