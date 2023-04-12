<?php

namespace A17\Twill\Tests\Browser;

use A17\Twill\Commands\Traits\HandlesPresets;
use A17\Twill\Models\User;
use A17\Twill\RouteServiceProvider;
use A17\Twill\TwillServiceProvider;
use A17\Twill\ValidationServiceProvider;
use App\Providers\AppServiceProvider;
use Carbon\Carbon;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverDimension;
use Illuminate\Support\Facades\DB;
use Kalnoy\Nestedset\NestedSetServiceProvider;
use Orchestra\Testbench\Dusk\TestCase;
use Throwable;
use Orchestra\Testbench\Dusk\Options as DuskOptions;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;

class BrowserTestCase extends TestCase
{
    use HandlesPresets;

    public ?string $example = null;
    public User $superAdmin;

    protected function getEnvironmentSetUp($app): void
    {
        Carbon::setTestNow(Carbon::now());

        $this->configTwill($app);
    }

    protected function getPackageProviders($app): array
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
            $list[] = AppServiceProvider::class;
        }

        return $list;
    }

    /**
     * Configure Twill options.
     *
     * @param $app
     */
    public function configTwill($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite.database', database_path('database.sqlite'));
        // Set the session driver
        $app['config']->set('session.driver', 'file');

        // Twill config.
        $app['config']->set('twill.admin_app_url', '');
        $app['config']->set('twill.admin_app_path', 'twill');
        $app['config']->set('twill.auth_login_redirect_path', '/twill');
        $app['config']->set('twill.enabled.users-2fa', true);
        $app['config']->set('twill.enabled.users-image', true);
        $app['config']->set('twill.auth_login_redirect_path', '/twill');
        $app['config']->set('translatable.locales', ['en', 'fr', 'pt-BR']);
    }

    public static function delTree($dir): bool
    {
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? self::delTree("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }

    public static function setUpBeforeClass(): void
    {
        cleanupTestState(self::applicationBasePath());
        parent::setUpBeforeClass();
    }

    public function tearDown(): void
    {
        cleanupTestState(self::applicationBasePath());
        parent::tearDown();
    }

    protected function onNotSuccessfulTest(Throwable $t): void
    {
        cleanupTestState(self::applicationBasePath());
        parent::onNotSuccessfulTest($t);
    }

    public function setUp(): void
    {
        $dbPath = self::getBasePathStatic() . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR;
        copy($dbPath . 'database.sqlite.example', $dbPath . 'database.sqlite');

        $this->backupDistFolder();

        if ($this->example) {
            $this->installPresetFiles(
                $this->example,
                true,
                $this->getBasePath()
            );
        }

        // Run the rest of the setup.
        parent::setUp();

        Carbon::setTestNow(Carbon::now());

        $this->configTwill($this->app);

        $this->installTwill();
    }

    public function backupDistFolder(): void
    {
        $this->restoreAndCleanupDistBackup();

        // Make a backup of the dist folder.
        $dirToCopy = __DIR__ . '/../../dist';
        $dirToCopyBackup = __DIR__ . '/../../dist-backup';

        shell_exec("cp -r $dirToCopy $dirToCopyBackup");
    }

    public function restoreAndCleanupDistBackup(): void
    {
        $dirToCopyBackup = __DIR__ . '/../../dist-backup';
        if (file_exists($dirToCopyBackup)) {
            $dirToCopy = __DIR__ . '/../../dist';

            shell_exec("rm -Rf $dirToCopy");
            shell_exec("cp -r $dirToCopyBackup $dirToCopy");

            shell_exec("rm -Rf $dirToCopyBackup");
        }
    }

    public function installTwill(): void
    {
        $this->truncateTwillUsers();

        $superAdmin = $this->makeNewSuperAdmin();

        $this->artisan('twill:install --no-interaction --fromBuild');

        $this->artisan('twill:superadmin ' . $superAdmin->email . ' ' . $superAdmin->password);

        $this->superAdmin = User::firstWhere('email', $superAdmin->email);
    }

    /**
     * Fake a super admin.
     */
    private function makeNewSuperAdmin(): User
    {
        $user = new User();

        $user->setAttribute('name', random_int(0, 5000));
        $user->setAttribute('email', random_int(0, 5000) . '@example.org');
        $user->setAttribute('password', 'admin');
        $user->setAttribute('unencrypted_password', 'admin');

        return $user;
    }

    protected function truncateTwillUsers(): void
    {
        try {
            DB::table('twill_users')->truncate();
        } catch (\Exception $exception) {
        }
    }

    protected static function getBasePathStatic(): string
    {
        return __DIR__ . '/../../vendor/orchestra/testbench-core/laravel';
    }

    public static function applicationBasePath(): string
    {
        return self::getBasePathStatic();
    }

    protected function getBasePath(): string
    {
        return __DIR__ . '/../../vendor/orchestra/testbench-core/laravel';
    }

    /**
     * @inheritDoc
     *
     * Override the default driver so that we can fix our resolution.
     */
    protected function driver(): RemoteWebDriver
    {
        if (DuskOptions::shouldUsesWithoutUI()) {
            DuskOptions::withoutUI();
        } elseif ($this->hasHeadlessDisabled()) {
            DuskOptions::withUI();
        }

        $driver = RemoteWebDriver::create(
            'http://localhost:9515',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY,
                DuskOptions::getChromeOptions()
            )
        );

        $driver->manage()->window()->setSize(new WebDriverDimension(1440, 900));

        return $driver;
    }
}
