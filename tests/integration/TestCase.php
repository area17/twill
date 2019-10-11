<?php

namespace A17\Twill\Tests\Integration;

use Faker\Factory as Faker;
use A17\Twill\AuthServiceProvider;
use A17\Twill\TwillServiceProvider;
use A17\Twill\RouteServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Console\Kernel;
use A17\Twill\ValidationServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
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
    private function boot($app)
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
        $user->password = 'secret';

        return $user;
    }

    /**
     * Instantiate Faker.
     */
    protected function instantiateFaker(): void
    {
        $this->faker = Faker::create('en_US');
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
            ValidationServiceProvider::class
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
        $app['config']->set('database.default', $connection = 'sqlite');

        $app['config']->set('activitylog.database_connection', $connection);

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

        if ($driver = $app['config']['database.connections.' . $connection . '.driver'] === 'sqlite') {
            $database = $app['config']['database.connections.' . $connection . '.database'];

            if (file_exists($database)) {
                unlink($database);
            }

            touch($database);
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
     * @return \A17\Twill\Models\User|\A17\Twill\Tests\Integration\UserClass
     */
    public function getSuperAdmin()
    {
        return $this->superAdmin = $this->superAdmin ?? $this->makeNewSuperAdmin();
    }

    /**
     * Clear and make needed directories in the Laravel directory.
     */
    protected function prepareLaravelDirectory()
    {
        array_map('unlink', glob($this->getBasePath() . '/database/migrations/*'));

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
             ->expectsQuestion('Enter a password', $this->getSuperAdmin()->password)
             ->expectsQuestion('Confirm the password', $this->getSuperAdmin()->password)
        ;
    }
}

class UserClass
{
    public $name;

    public $email;

    public $password;
}
