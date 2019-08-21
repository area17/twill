<?php

namespace A17\Twill\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\DatabaseManager;
use Illuminate\Filesystem\Filesystem;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twill:install';

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

        $this->addRoutesFile();
        $this->publishMigrations();
        $this->call('migrate');
        $this->publishConfig();
        $this->publishAssets();
        $this->createSuperAdmin();
        $this->info('All good!');
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
     * Publishes the previously created package migration files.
     *
     * @return void
     */
    private function publishMigrations()
    {
        $this->call('vendor:publish', [
            '--provider' => 'A17\Twill\TwillServiceProvider',
            '--tag' => 'migrations',
        ]);
    }

    /**
     * Calls the command responsible for creation of the default superadmin user.
     *
     * @return void
     */
    private function createSuperAdmin()
    {
        $this->call('twill:superadmin');
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
