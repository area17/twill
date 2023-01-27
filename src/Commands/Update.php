<?php

namespace A17\Twill\Commands;

use Illuminate\Filesystem\Filesystem;

class Update extends Command
{
    protected $signature = 'twill:update {--fromBuild} {--migrate}';
    protected $description = 'Publish new updated Twill assets and optionally run database migrations';

    public function __construct(public Filesystem $files)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $this->publishAssets();
        $this->call('twill:flush-manifest');
        $this->call('view:clear');
        if ($this->option('migrate') || $this->confirm('Do you want to run any pending database migrations now?')) {
            $this->call('migrate');
        }
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
                '--force' => true,
            ]);
        }
    }
}
