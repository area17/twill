<?php

namespace A17\Twill\Commands;

class Update extends Command
{
    /**
     * @var string
     */
    protected $signature = 'twill:update';

    /**
     * @var string
     */
    protected $description = 'Publish new updated Twill assets';

    /**
     * Executes the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        $this->publishAssets();
        $this->call('cache:clear');
        $this->call('view:clear');
    }

    /**
     * Publishes the package frontend assets.
     */
    private function publishAssets(): void
    {
        $this->call('vendor:publish', [
            '--provider' => \A17\Twill\TwillServiceProvider::class,
            '--tag' => 'assets',
            '--force' => true,
        ]);
    }
}
