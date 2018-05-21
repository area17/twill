<?php

namespace A17\Twill\Commands;

use File;
use Illuminate\Console\Command;

class Setup extends Command
{
    protected $signature = 'twill:setup';

    protected $description = 'Setup Twill superadmin and publish assets/configs';

    public function handle()
    {
        $this->publishMigrations();
        $this->call('migrate');
        $this->publishAssets();
        $this->publishConfig();
        $this->createSuperAdmin();
    }

    private function publishMigrations()
    {
        $defaultMigrations = [
            '2014_10_12_000000_create_users_table.php',
            '2014_10_12_100000_create_password_resets_table.php',
        ];

        foreach ($defaultMigrations as $migration) {
            $fullPath = database_path('migrations/' . $migration);
            if (File::exists($fullPath)) {
                File::delete($fullPath);
            }
        }

        $this->call('vendor:publish', [
            '--provider' => 'A17\Twill\TwillServiceProvider',
            '--tag' => 'migrations',
        ]);

        $this->call('vendor:publish', [
            '--provider' => 'Spatie\Activitylog\ActivitylogServiceProvider',
            '--tag' => 'migrations',
        ]);
    }

    private function createSuperAdmin()
    {
        $this->call('twill:superadmin');
    }

    private function publishAssets()
    {
        $this->call('vendor:publish', [
            '--provider' => 'A17\Twill\TwillServiceProvider',
            '--tag' => 'assets',
        ]);
    }

    private function publishConfig()
    {
        $this->call('vendor:publish', [
            '--provider' => 'A17\Twill\TwillServiceProvider',
            '--tag' => 'config',
        ]);

        $this->call('vendor:publish', [
            '--provider' => "Dimsav\Translatable\TranslatableServiceProvider",
        ]);
    }
}
