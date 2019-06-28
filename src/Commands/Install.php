<?php

namespace A17\Twill\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Database\DatabaseManager;

class Install extends Command
{
    protected $signature = 'twill:install';

    protected $description = 'Install Twill into your Laravel application';

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

    public function handle()
    {
        //check the database connection before installing
        try {
            $this->db->connection()->getPdo();
        } catch (\Exception $e) {
            $this->error('Could not connect to the database, please check your configuration:' . "\n" . $e);
            return;
        }

        $this->addRoutesFile();
        $this->publishMigrations();
        $this->call('migrate');
        $this->publishConfig();
        $this->publishAssets();
        $this->createSuperAdmin();
        $this->info('All good!');
    }

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

    private function publishMigrations()
    {
        $this->call('vendor:publish', [
            '--provider' => 'A17\Twill\TwillServiceProvider',
            '--tag' => 'migrations',
        ]);

        if (!class_exists('CreateActivityLogTable')) {
            $this->call('vendor:publish', [
                '--provider' => 'Spatie\Activitylog\ActivitylogServiceProvider',
                '--tag' => 'migrations',
            ]);
        }
    }

    private function createSuperAdmin()
    {
        $this->call('twill:superadmin');
    }

    private function publishConfig()
    {
        $this->call('vendor:publish', [
            '--provider' => 'A17\Twill\TwillServiceProvider',
            '--tag' => 'config',
        ]);
    }

    private function publishAssets()
    {
        $this->call('vendor:publish', [
            '--provider' => 'A17\Twill\TwillServiceProvider',
            '--tag' => 'assets',
        ]);
    }

}
