<?php

namespace A17\Twill\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class FEPublish extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twill:fe-publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Publish twill FE build";

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();

        $this->filesystem = $filesystem;
    }

    /*
     * Executes the console command.
     *
     */
    public function handle()
    {
        $options = [
            '--provider' => 'A17\Twill\TwillServiceProvider',
            '--tag' => 'assets',
            '--force' => '--force'
        ];

        $this->call('vendor:publish', $options);
    }
}
