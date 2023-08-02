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
use Faker\Generator;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Kalnoy\Nestedset\NestedSetServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Throwable;

abstract class TestCase extends OrchestraTestCase
{
    use CopyBlocks;
    use HandlesPresets;

    public static bool $didInitialFreshMigration = false;

    public const DEFAULT_PASSWORD = 'secret';

    public const DEFAULT_LOCALE = 'en_US';

    public Generator $faker;

    public ?string $example = null;

    public ?User $superAdmin = null;

    public Carbon $now;

    public int $recursiveCounter = 0;

    public TestResponse $crawler;

    /**
     * After a long debugging session I found that this flow is the most stable.
     * Running the example installer in the setup would cause the files to be not on time when tests shift from
     * one example to another.
     */
    public function createApplication(): Application
    {
        $app = $this->resolveApplication();

        $this->resolveApplicationBindings($app);
        $this->resolveApplicationExceptionHandler($app);
        $this->resolveApplicationCore($app);
        $this->resolveApplicationConfiguration($app);
        $this->resolveApplicationHttpKernel($app);
        $this->resolveApplicationConsoleKernel($app);
        $this->resolveApplicationBootstrappers($app);

        return $app;
    }

    public static function setUpBeforeClass(): void
    {
        cleanupTestState(self::applicationBasePath());
        parent::setUpBeforeClass();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        cleanupTestState(self::applicationBasePath());
    }

    protected function onNotSuccessfulTest(Throwable $t): void
    {
        // When a test fails it doesnt run teardown.
        $this->tearDown();

        parent::onNotSuccessfulTest($t);
    }

    /**
     * Setup tests.
     */
    public function setUp(): void
    {
        if ($this->example) {
            $this->installPresetFiles(
                $this->example,
                true,
                $this->getBasePath()
            );
        }

        // Enforce the url for testing to be 'http://twill.test' for certain assertions.
        // This is different from the one in phpunit.xml because that one is used for laravel dusk.
        $_ENV['APP_URL'] = 'http://twill.test';
        $_ENV['MEDIA_LIBRARY_LOCAL_PATH'] = "media-library";
        $_ENV["FILE_LIBRARY_LOCAL_PATH"] = "file-library";
        $_ENV["FILE_LIBRARY_ENDPOINT_TYPE"] = "local";
        $_ENV["IMGIX_SOURCE_HOST"] = "";
        $_ENV["IMGIX_USE_HTTPS"] = "";
        $_ENV["IMGIX_USE_SIGNED_URLS"] = "";
        $_ENV["IMGIX_SIGN_KEY"] = "";
        $_ENV["GLIDE_SOURCE"] = $this->getBasePath() . "/storage/app/public/media-library";
        $_ENV["GLIDE_CACHE"] = $this->getBasePath() . "/storage/app/twill/cache";
        $_ENV["GLIDE_CACHE_PATH_PREFIX"] = "glide_cache";
        $_ENV["GLIDE_BASE_URL"] = "http://twill.test";
        $_ENV["GLIDE_BASE_PATH"] = "storage/media-library";
        $_ENV["GLIDE_USE_SIGNED_URLS"] = "false";
        $_ENV["GLIDE_SIGN_KEY"] = "";

        parent::setUp();

        if (!self::$didInitialFreshMigration) {
            $this->artisan('migrate:fresh');
        }

        $this->loadConfig();

        $this->instantiateFaker();

        $this->copyBlocks();

        $this->installTwill();

        // Add database seeders to autoload as it is not in the orchestra base composer.
        foreach (File::allFiles(base_path('/database/seeders')) as $file) {
            include_once $file->getPathname();
        }
    }

    /**
     * Configure Twill options.
     *
     * @param $app
     */
    public function configTwill($app): void
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
     * Configure storage path.
     *
     * @param $app
     */
    public function configureStorage($app): void
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
     * Login the current SuperUser.
     */
    protected function login(): TestResponse
    {
        $this->request('/twill/login', 'POST', [
            'email' => $this->superAdmin()->email,
            'password' => $this->superAdmin()->unencrypted_password,
        ]);

        return $this->crawler;
    }

    /**
     * Login with the provided credentials.
     */
    protected function loginAs(string $email, string $password): TestResponse
    {
        $this->request('/twill/login', 'POST', [
            'email' => $email,
            'password' => $password,
        ]);

        return $this->crawler;
    }

    /**
     * Fake a super admin.
     */
    public function makeNewSuperAdmin(): User
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
     */
    protected function getPackageProviders($app)
    {
        app()->instance(
            'autoloader',
            require __DIR__ . '/../../vendor/autoload.php'
        );

        $list = [
            RouteServiceProvider::class,
            TwillServiceProvider::class,
            ValidationServiceProvider::class,
            NestedSetServiceProvider::class,
        ];

        if ($this->example && file_exists(app_path('Providers/AppServiceProvider.php'))) {
//            $list[] = AppServiceProvider::class;
        }

        return $list;
    }

    /**
     * Define environment setup.
     */
    protected function getEnvironmentSetUp($app)
    {
        Carbon::setTestNow($this->now = Carbon::now());

        $this->configureStorage($app);

        $this->configTwill($app);
    }

    /**
     * Get or make a super admin.
     */
    public function superAdmin(bool $force = false): User
    {
        return $this->superAdmin =
            ! $this->superAdmin || $force
                ? $this->makeNewSuperAdmin()
                : $this->superAdmin;
    }

    /**
     * Install Twill.
     */
    public function installTwill(): void
    {
        $this->artisan('twill:install --no-interaction');
        $this->artisan('twill:superadmin ' . $this->superAdmin()->email . ' ' . $this->superAdmin()->password);

        $user = User::where('email', $this->superAdmin()->email)->first();

        $user->setAttribute(
            'unencrypted_password',
            $this->superAdmin->unencrypted_password
        );

        $this->superAdmin = $user;
    }

    /**
     * Delete a directory.
     */
    public function deleteDirectory(string $param): void
    {
        if ($this->files->exists($param)) {
            $this->files->deleteDirectory($param);
        }
    }

    /**
     * Get a collection with all routes.
     */
    public function getAllRoutes(?string $method = null): Collection
    {
        $routes = Route::getRoutes();

        if ($method) {
            $routes = $routes->get($method);
        }

        return collect($routes)->filter(function ($route) {
            return is_callable($route->action['uses']) || Str::startsWith($route->action['uses'], 'A17\Twill') ||
                Str::startsWith($route->action['uses'], 'App\\');
        });
    }

    /**
     * Get a collection with all package uris.
     */
    public function getAllUris(): Collection
    {
        return $this->getAllRoutes()
            ->pluck('uri', 'action.as')
            ->sort()
            ->unique()
            ->values();
    }

    public function getAllUrisWithName(): Collection
    {
        return $this->getAllRoutes()->map(function ($route, $index) {
            return [$route->action['as'] ?? $index => $route->uri];
        })->all();
    }

    /**
     * Get a collection with all package uris.
     */
    public function getUriWithNames(): Collection
    {
        return $this->getAllRoutes()->pluck('uri', 'action.as');
    }

    /**
     * Send request to an ajax route.
     */
    public function request(
        string $uri,
        string $method = 'GET',
        array $parameters = [],
        array $cookies = [],
        array $files = [],
        array $server = [],
        ?string $content = null,
        bool $followRedirects = true
    ): TestResponse {
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

    public function ajax(
        string $uri,
        string $method = 'GET',
        array $parameters = [],
        array $cookies = [],
        array $files = [],
        array $server = [],
        ?string $content = null,
        bool $followRedirects = true
    ): TestResponse {
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
     * Replace placeholders to make a filename.
     */
    public function makeFileName(string $file, ?string $source = null): string
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

        if (filled($source) && ! Str::endsWith($file, ".{$extension}")) {
            $file .= basename($source);
        }

        return $file;
    }

    /**
     * Return the contents from current crawler response.
     */
    public function content(): bool|string
    {
        return $this->crawler->getContent();
    }

    /**
     * Assert can see text.
     */
    public function assertSee(?string $text): void
    {
        $this->assertStringContainsString(
            clean_file($text),
            clean_file($this->content())
        );
    }

    /**
     * Assert cannot see text.
     */
    public function assertDontSee(string $text): void
    {
        $this->assertStringNotContainsString(
            clean_file($text),
            clean_file($this->content())
        );
    }

    /**
     * Assert a successful exit code.
     */
    public function assertExitCodeIsGood(int $exitCode): void
    {
        $this->assertFalse($exitCode !== 0);
        if ($exitCode !== 0) {
            throw new Exception(
                "Test ended with exit code {$exitCode}. Non-fatal errors possibly happened during tests."
            );
        }
    }

    /**
     * Assert a failing exit code.
     */
    public function assertExitCodeIsNotGood(int $exitCode): void
    {
        if ($exitCode === 0) {
            throw new Exception(
                "Test ended with exit code 0, but this wasn't supposed to happen!"
            );
        }
    }

    public function getCommand(string $commandName): mixed
    {
        return $this->app->make(Kernel::class)->all()[$commandName];
    }

    public function httpJsonRequestAssert(
        string $url,
        string $method = 'GET',
        array $data = [],
        int $expectedStatusCode = 200
    ): TestResponse {
        $response = $this->json(
            $method,
            $url,
            $data
        );

        $this->assertLogStatusCode($response, $expectedStatusCode);

        $response->assertStatus($expectedStatusCode);

        return $response;
    }

    public function httpRequestAssert(
        string $url,
        string $method = 'GET',
        array $data = [],
        int $expectedStatusCode = 200
    ): TestResponse {
        $response = $this->request(
            $url,
            $method,
            $data
        );

        $this->assertLogStatusCode($response, $expectedStatusCode);

        $response->assertStatus($expectedStatusCode);

        return $response;
    }

    public function assertLogStatusCode(TestResponse $response, int $expectedStatusCode = 200): void
    {
        if ($response->getStatusCode() !== $expectedStatusCode) {
            var_dump('------------------- ORIGINAL RESPONSE');
            var_dump($response->getContent());
        }
    }

    protected function assertNothingWrongHappened(): void
    {
        $this->assertDontSee('Something wrong happened!');
    }

    public function loadConfig()
    {
    }
}
