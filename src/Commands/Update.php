<?php

namespace A17\Twill\Commands;

use Illuminate\Filesystem\Filesystem;

class Update extends Command
{
    protected $signature = 'twill:update {--fromBuild}';
    protected $description = 'Publish new updated Twill assets';

    public function __construct(public Filesystem $files)
    {
        parent::__construct();
    }

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
