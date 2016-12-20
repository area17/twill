<?php

namespace A17\CmsToolkit\Commands;

use File;
use Illuminate\Console\Command;

class Setup extends Command
{
    protected $signature = 'cms-toolkit:setup';

    protected $description = 'Setup the CMS Toolkit superadmin and publish assets/configs';

    public function fire()
    {
        $this->publishMigrations();
        $this->call('migrate');
        $this->createSuperAdmin();
        $this->publishAssets();
        $this->publishConfigs();
    }

    private function publishMigrations()
    {
        $defaultMigrations = [
            '2014_10_12_000000_create_users_table.php',
            '2014_10_12_100000_create_password_resets_table.php',
        ];

        foreach ($defaultMigrations as $migration) {
            $fullPath = database_path('migrations/' . $migration);
            if (File::has($fullPath)) {
                File::delete($fullPath);
            }
        }

        $this->call('vendor:publish', [
            '--provider' => 'A17\CmsToolkit\CmsToolkitServiceProvider',
            '--tag' => 'migrations',
        ]);
    }

    private function createSuperAdmin()
    {
        $this->call('cms-toolkit:superadmin');
    }

    private function publishAssets()
    {
        $this->call('vendor:publish', [
            '--provider' => 'A17\CmsToolkit\CmsToolkitServiceProvider',
            '--tag' => 'assets',
        ]);
    }

    private function publishConfig()
    {
        $this->call('vendor:publish', [
            '--provider' => 'A17\CmsToolkit\CmsToolkitServiceProvider',
            '--tag' => 'config',
        ]);
    }
}
