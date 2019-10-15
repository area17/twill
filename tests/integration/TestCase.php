<?php

namespace A17\Twill\Tests\Integration;

use Faker\Factory as Faker;
use Illuminate\Support\Str;
use A17\Twill\AuthServiceProvider;
use A17\Twill\TwillServiceProvider;
use A17\Twill\RouteServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Console\Kernel;
use A17\Twill\ValidationServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
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
     * @var \A17\Twill\Models\User
     */
    public $superAdmin;

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    public $files;

    private function configTwill($app)
    {
        $app['config']->set('twill.admin_app_url', '');
        $app['config']->set('twill.admin_app_path', 'twill');
        $app['config']->set('twill.auth_login_redirect_path', '/twill');
    }

    /**
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
     * Setup tests.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->instantiateFaker();

        $this->installTwill();
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function boot($app)
    {
        $this->files = $app->make(Filesystem::class);

        $this->prepareLaravelDirectory();
    }

    /**
     * Fake a super admin.
     *
     * @return \A17\Twill\Tests\Integration\UserClass
     */
    public function makeNewSuperAdmin()
    {
        $user = new UserClass();

        $user->name = $this->faker->name;
        $user->email = $this->faker->email;
        $user->password = self::DEFAULT_PASSWORD;

        return $user;
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
    public function getSuperAdmin($force = false)
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

        if (!file_exists($directory = twill_path('Http/Controllers'))) {
            $this->files->makeDirectory($directory, 744, true);
        }
    }

    /**
     * Install Twill.
     */
    public function installTwill()
    {
        $this->artisan('twill:install')
            ->expectsQuestion('Enter an email', $this->getSuperAdmin()->email)
            ->expectsQuestion(
                'Enter a password',
                $this->getSuperAdmin()->password
            )
            ->expectsQuestion(
                'Confirm the password',
                $this->getSuperAdmin()->password
            );
    }

    public function deleteDirectory(string $param)
    {
        if ($this->files->exists($param)) {
            $this->files->deleteDirectory($param);
        }
    }

    public function getAllRoutes()
    {
        return collect(Route::getRoutes());
    }

    public function getAllUris()
    {
        return $this->getAllRoutes()
            ->filter(function ($route) {
                return Str::startsWith($route->action['uses'], 'A17\Twill');
            })
            ->pluck('uri')
            ->sort()
            ->unique()
            ->values();
    }
}

class UserClass
{
    public $name;

    public $email;

    public $password;
}
