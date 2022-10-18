<?php

namespace A17\Twill\Commands;

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
        $this->call('twill:flush-manifest');
        $this->call('view:clear');
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
            '--force' => true,
        ]);
    }
}
