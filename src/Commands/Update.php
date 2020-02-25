<?php

namespace A17\Twill\Commands;

use Illuminate\Console\Command;

class Update extends Command
{
    protected $signature = 'twill:update';

    protected $description = 'Publish new updated Twill assets';

    /**
     * Executes the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->publishAssets();
        $this->info('You should now also run php artisan migrate to execute any new Twill provided migration.');
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
