<?php

namespace A17\Twill\Commands;

use Illuminate\Filesystem\Filesystem;

class Update extends Command
{
    protected $signature = 'twill:update {--fromBuild}';

    protected $description = 'Publish new updated Twill assets';

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    private $files;

    /**
     * Create a new command instance.
     *
     * @param \Illuminate\Filesystem\Filesystem $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Executes the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->publishAssets();
        $this->call('cache:clear');
        $this->call('view:clear');
    }

    /**
     * Publishes the package frontend assets.
     *
     * @return void
     */
    private function publishAssets()
    {
        if ($this->option('fromBuild')) {
            // If this is from a build, we copy from dist to public.
            $this->files->copyDirectory(__DIR__ . '/../../dist/', public_path());
        } else {
            // Otherwise we simply publish the production assets.
            $this->call('vendor:publish', [
                '--provider' => 'A17\Twill\TwillServiceProvider',
                '--tag' => 'assets',
                '--force' => true,
            ]);
        }
    }
}
