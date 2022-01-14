<?php

namespace A17\Twill\Commands;

use Illuminate\Database\DatabaseManager;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twill:install {preset?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Twill into your Laravel application';

    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * @var DatabaseManager
     */
    protected $db;

    /**
     * @param Filesystem $files
     * @param DatabaseManager $db
     */
    public function __construct(Filesystem $files, DatabaseManager $db)
    {
        parent::__construct();

        $this->files = $files;
        $this->db = $db;
    }

    /**
     * Executes the console command.
     *
     * @return void
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        //check the database connection before installing
        try {
            $this->db->connection()->getPdo();
        } catch (\Exception $e) {
            $this->error('Could not connect to the database, please check your configuration:' . "\n" . $e);
            return;
        }

        if (filled($preset = $this->argument('preset'))) {
            if ($this->presetExists($preset)) {
                if ($this->confirm(
                    'Are you sure to install this preset? This can overwrite your models, config and routes.'
                )) {
                    $this->installPreset($preset);
                } else {
                    $this->warn('Cancelled.');
                }
            } else {
                $this->error("Could not find preset: $preset, available presets are: 'blog'");
            }
        } else {
            $this->addRoutesFile();
            $this->call('migrate');
            $this->publishConfig();
            $this->publishAssets();
            $this->createSuperAdmin();
            $this->info('All good!');
        }
    }

    private function presetExists(string $preset): bool
    {
        return in_array($preset, ['blog'], true);
    }

    private function installPreset(string $preset): void
    {
        $this->info("Installing $preset preset");

        $storage = Storage::build([
            'driver' => 'local',
            'root' => __DIR__ . '/../../examples/' . $preset,
        ]);
        $appPathStorage = Storage::build([
            'driver' => 'local',
            'root' => base_path(),
        ]);

        foreach ($storage->allDirectories() as $directory) {
            if ($appPathStorage->makeDirectory($directory)) {
                foreach ($storage->files($directory) as $file) {
                    $appPathStorage->put($file, $storage->get($file));
                }
            }
        }

        $this->call('migrate');
        $this->createSuperAdmin();
        $this->info('Finished installing preset!');
    }

    /**
     * Creates the default `admin.php` route configuration file.
     *
     * @return void
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function addRoutesFile()
    {
        $routesPath = base_path('routes');

        if (!$this->files->exists($routesPath)) {
            $this->files->makeDirectory($routesPath, 0755, true);
        }

        if (!$this->files->exists($routesPath . '/admin.php')) {
            $stub = $this->files->get(__DIR__ . '/stubs/admin.stub');
            $this->files->put($routesPath . '/admin.php', $stub);
        }
    }

    /**
     * Calls the command responsible for creation of the default superadmin user.
     *
     * @return void
     */
    private function createSuperAdmin()
    {
        if (!$this->option('no-interaction')) {
            $this->call('twill:superadmin');
        }
    }

    /**
     * Publishes the package configuration files.
     *
     * @return void
     */
    private function publishConfig()
    {
        $this->call('vendor:publish', [
            '--provider' => 'A17\Twill\TwillServiceProvider',
            '--tag' => 'config',
        ]);
    }

    /**
     * Publishes the package frontend assets.
     *
     * @return void
     */
    private function publishAssets()
    {
        $this->call('vendor:publish', [
            '--provider' => 'A17\Twill\TwillServiceProvider',
            '--tag' => 'assets',
        ]);
    }

}
