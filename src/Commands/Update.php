<?php

namespace A17\Twill\Commands;

use Illuminate\Console\Command;

class Update extends Command
{
    protected $signature = 'twill:update';

    protected $description = 'Publish new Twill migrations';

    public function handle()
    {
        $this->publishMigrations();
        $this->info('You should now run php artisan migrate.');
    }

    private function publishMigrations()
    {
        $this->call('vendor:publish', [
            '--provider' => 'A17\Twill\TwillServiceProvider',
            '--tag' => 'twill-updates-migrations',
        ]);
    }
}
