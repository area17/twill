<?php

namespace A17\Twill\Commands;

use A17\Twill\Commands\Traits\HandlesPresets;
use Illuminate\Database\DatabaseManager;
use Illuminate\Filesystem\Filesystem;

class Install extends Command
{
    use HandlesPresets;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twill:install {preset? : Optional, the preset to install} {--fromBuild}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Twill into your Laravel application';

    public function __construct(public Filesystem $files, public DatabaseManager $db)
    {
        parent::__construct();
    }

    /**
     * Executes the console command.
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle(): void
    {
        //check the database connection before installing
        try {
            $this->db->connection()->getPdo();
        } catch (\Exception $exception) {
            $this->error('Could not connect to the database, please check your configuration:' . "\n" . $exception);

            return;
        }

        if (filled($preset = $this->argument('preset'))) {
            if ($this->presetExists($preset)) {
                if (
                    $this->confirm(
                        'Are you sure to install this preset? This can overwrite your models, config and routes.'
                    )
                ) {
                    $this->installPreset($preset);
                } else {
                    $this->warn('Cancelled.');
                }
            } else {
                $this->error("Could not find preset: $preset");
            }
        } else {
            $this->copyBlockPreviewFile();
            $this->addRoutesFile();
            $this->call('migrate');
            $this->publishConfig();
            $this->publishAssets();
            $this->createSuperAdmin();
            $this->info('All good!');
        }
    }

    private function installPreset(string $preset): void
    {
        $this->installPresetFiles($preset);

        $this->call('migrate');
        $this->publishAssets();
        $this->createSuperAdmin();
        $this->info('Finished installing preset!');
    }

    /**
     * Creates the default `twill.php` route configuration file.
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function addRoutesFile(): void
    {
        $routesPath = base_path('routes');

        if (!$this->files->exists($routesPath)) {
            $this->files->makeDirectory($routesPath, 0755, true);
        }

        if (!$this->files->exists($routesPath . '/twill.php')) {
            $stub = $this->files->get(__DIR__ . '/stubs/admin.stub');
            $this->files->put($routesPath . '/twill.php', $stub);
        }
    }

    private function copyBlockPreviewFile(): void
    {
        $layoutsDirectory = base_path('resources/views/site/layouts');

        if (!$this->files->exists($layoutsDirectory)) {
            $this->files->makeDirectory($layoutsDirectory, 0755, true);
        }

        if (!$this->files->exists($layoutsDirectory . '/block.blade.php')) {
            $stub = $this->files->get(__DIR__ . '/stubs/block.blade.php');
            $this->files->put($layoutsDirectory . '/block.blade.php', $stub);
        }
    }

    /**
     * Calls the command responsible for creation of the default superadmin user.
     */
    private function createSuperAdmin(): void
    {
        if (!$this->option('no-interaction')) {
            $this->call('twill:superadmin');
        }
    }

    /**
     * Publishes the package configuration files.
     */
    private function publishConfig(): void
    {
        $this->call('vendor:publish', [
            '--provider' => \A17\Twill\TwillServiceProvider::class,
            '--tag' => 'config',
        ]);
    }

    /**
     * Publishes the package frontend assets.
     */
    private function publishAssets(): void
    {
        if ($this->option('fromBuild')) {
            // If this is from a build, we copy from dist to public.
            $this->files->copyDirectory(__DIR__ . '/../../dist/', public_path());
        } else {
            $this->call('vendor:publish', [
                '--provider' => \A17\Twill\TwillServiceProvider::class,
                '--tag' => 'assets',
            ]);
        }
    }
}
