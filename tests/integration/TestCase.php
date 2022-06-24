<?php

namespace A17\Twill\Tests\Integration;

use A17\Twill\Commands\Traits\HandlesPresets;
use A17\Twill\Models\User;
use A17\Twill\RouteServiceProvider;
use A17\Twill\Tests\Integration\Behaviors\CopyBlocks;
use A17\Twill\TwillServiceProvider;
use A17\Twill\ValidationServiceProvider;
use Carbon\Carbon;
use Exception;
use Faker\Factory as Faker;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Kalnoy\Nestedset\NestedSetServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    use CopyBlocks;
    use HandlesPresets;

    public const DATABASE_MEMORY = ':memory:';

    public const DEFAULT_PASSWORD = 'secret';

    public const DEFAULT_LOCALE = 'en_US';

    public const DB_CONNECTION = 'sqlite';

    /**
     * @var \Faker\Generator
     */
    public $faker;

    /**
     * @var string|null
     */
    public $example;

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
        'Http/Controllers/Twill',
        'Http/Requests/Twill',
        'Models/Revisions',
        'Models/Slugs',
        'Models/Translations',
        'Repositories',
        '/../resources/views/twill/authors',
        '/../resources/views/twill/categories',
        '/../resources/views/site/blocks',
        '/../resources/views/site/layouts',
    ];

    public $toDelete = [
        '{$database}/migrations/',
        '{$base}/routes/twill.php',
        '{$app}/Models',
        '{$app}/Http',
        '{$app}/Repositories/',
        '{$resources}/views',
        '{$database}/migrations',
        '{$app}/Twill',
        '{$routes}',
        '{$config}/twill-navigation.php',
        '{$config}/twill.php',
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
     * After a long debugging session I found that this flow is the most stable.
     * Running the example installer in the setup would cause the files to be not on time when tests shift from
     * one example to another.
     */
    public function createApplication()
    {
        $app = $this->resolveApplication();

        $this->resolveApplicationBindings($app);
        $this->resolveApplicationExceptionHandler($app);
        $this->resolveApplicationCore($app);
        $this->resolveApplicationConfiguration($app);
        $this->resolveApplicationHttpKernel($app);
        $this->resolveApplicationConsoleKernel($app);

        if ($this->example) {
            $this->installPresetFiles($this->example, true);
        }

        $this->resolveApplicationBootstrappers($app);

        return $app;
    }

    /**
     * Setup tests.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->loadConfig();

        $this->freshDatabase();

        $this->cleanDirectories();

        $this->instantiateFaker();

        $this->copyBlocks();

        $this->copyTestFiles();

        $this->installTwill();

        // Add database seeders to autoload as it is not in the orchestra base composer.
        foreach (File::allFiles(base_path("/database/seeders")) as $file) {
            include_once $file->getPathname();
        }
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
        $app['config']->set('translatable.locales', ['en', 'fr', 'pt-BR']);
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
        ]);

        return $this->crawler;
    }

    /**
     * Login with the provided credentials.
     *
     * @param string $email
     * @param string $password
     * @return \Illuminate\Foundation\Testing\TestResponse|void
     */
    protected function loginAs($email, $password)
    {
        $this->request('/twill/login', 'POST', [
            'email' => $email,
            'password' => $password,
        ]);

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
        app()->instance(
            'autoloader',
            require __DIR__ . '/../../vendor/autoload.php'
        );

        return [
            RouteServiceProvider::class,
            TwillServiceProvider::class,
            ValidationServiceProvider::class,
            NestedSetServiceProvider::class,
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

        if ($app['config']['database.connections.' . $connection . '.driver'] === self::DB_CONNECTION) {
            $this->createDatabase($app['config']['database.connections.' . $connection . '.database']);
        }
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

    public function copyTestFiles(): void
    {
        if (isset($this->allFiles)) {
            $this->copyFiles($this->allFiles);
        }
    }

    /**
     * Install Twill.
     */
    public function installTwill(): void
    {
        $this->truncateTwillUsers();

        if ($this->example) {
            $this->artisan('twill:install ' . $this->example)
                ->expectsConfirmation(
                    'Are you sure to install this preset? This can overwrite your models, config and routes.',
                    'yes'
                )
                ->expectsQuestion('Enter an email', $this->superAdmin()->email)
                ->expectsQuestion('Enter a password', $this->superAdmin()->password)
                ->expectsQuestion(
                    'Confirm the password',
                    $this->superAdmin()->password
                );
        } else {
            $this->artisan('twill:install')
                ->expectsQuestion('Enter an email', $this->superAdmin()->email)
                ->expectsQuestion('Enter a password', $this->superAdmin()->password)
                ->expectsQuestion(
                    'Confirm the password',
                    $this->superAdmin()->password
                );
        }

        $user = User::where('email', $this->superAdmin()->email)->first();

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

        $response = $this->request(
            $uri,
            $method,
            $parameters,
            $cookies,
            $files,
            $server,
            $content,
            $followRedirects
        );

        $this->assertLogStatusCode($response);

        return $response;
    }

    /**
     * Freeze time.
     */
    public function freezeTime($callback = null)
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
            collect($destination)->each(function ($destination) use ($source) {
                $source = $this->makeFileName($source);

                $destination = $this->makeFileName($destination, $source);

                if (!$this->files->exists($directory = dirname($destination))) {
                    $this->files->makeDirectory($directory, 0755, true);
                }

                if ($this->files->exists($destination)) {
                    $this->files->delete($destination);
                }

                $this->files->copy($source, $destination);
            });
        });
    }

    public function cleanDirectories()
    {
        collect($this->toDelete ?? [])->each(function ($directory) {
            $file = $this->makeFileName($directory);

            if (is_dir($file)) {
                File::deleteDirectory($file);
            }

            if (!is_dir($file) && file_exists($file)) {
                unlink($file);
            }

            if (!Str::endsWith($file, '.php')) {
                File::makeDirectory($file, 0755, true);
            }
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
                '{$vendor}',
                '{$tests}',
                '{$routes}',
            ],
            [
                stubs(),
                database_path(),
                base_path(),
                app_path(),
                resource_path(),
                config_path(),
                base_path('vendor'),
                __DIR__,
                base_path('routes'),
            ],
            $file
        );

        $extension = pathinfo($file, PATHINFO_EXTENSION);

        if (filled($source) && !Str::endsWith($file, ".{$extension}")) {
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
     * Skip test if running on Travis.
     */
    public function skipOnTravis()
    {
        if (!is_null(env('TRAVIS_PHP_VERSION'))) {
            $this->markTestSkipped('This test cannot be executed on Travis');
        }
    }

    /**
     * Assert a successful exit code.
     *
     * @param int $exitCode
     * @throws \Exception
     */
    public function assertExitCodeIsGood($exitCode)
    {
        if ($exitCode !== 0) {
            throw new Exception(
                "Test ended with exit code {$exitCode}. Non-fatal errors possibly happened during tests."
            );
        }
    }

    /**
     * Assert a failing exit code.
     *
     * @param int $exitCode
     * @throws \Exception
     */
    public function assertExitCodeIsNotGood($exitCode)
    {
        if ($exitCode === 0) {
            throw new Exception(
                "Test ended with exit code 0, but this wasn't supposed to happen!"
            );
        }
    }

    public function getCommand($commandName)
    {
        return $this->app->make(Kernel::class)->all()[$commandName];
    }

    public function httpJsonRequestAssert($url, $method = 'GET', $data = [], $expectedStatusCode = 200)
    {
        $response = $this->json(
            $method,
            $url,
            $data
        );

        $this->assertLogStatusCode($response, $expectedStatusCode);

        $response->assertStatus($expectedStatusCode);

        return $response;
    }

    public function httpRequestAssert($url, $method = 'GET', $data = [], $expectedStatusCode = 200)
    {
        $response = $this->request(
            $url,
            $method,
            $data
        );

        $this->assertLogStatusCode($response, $expectedStatusCode);

        $response->assertStatus($expectedStatusCode);

        return $response;
    }

    public function assertLogStatusCode(TestResponse $response, $expectedStatusCode = 200)
    {
        if ($response->getStatusCode() !== $expectedStatusCode) {
            var_dump('------------------- ORIGINAL RESPONSE');
            var_dump($response->getContent());
        }
    }

    public function freshDatabase()
    {
        if (file_exists($file = env('DB_DATABASE'))) {
            unlink($file);
            touch($file);
        }
    }

    protected function truncateTwillUsers(): void
    {
        try {
            DB::table('twill_users')->truncate();
        } catch (Exception $exception) {
        }
    }

    protected function assertNothingWrongHappened()
    {
        $this->assertDontSee('Something wrong happened!');
    }

    /**
     * Migrate database.
     */
    public function migrate()
    {
        $this->artisan('migrate');
    }

    public function loadConfig()
    {
    }
}
