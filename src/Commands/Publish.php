<?php

namespace A17\Twill\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class Publish extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twill:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Publish Twill assets (experimental)";

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
     * @return mixed
     */
    public function handle()
    {
        $progressBar = $this->output->createProgressBar(4);
        $this->filesystem->copyDirectory(base_path('vendor/area17/twill/dist'), public_path());
        $progressBar->setMessage("Done.");
        $progressBar->finish();
    }
}
