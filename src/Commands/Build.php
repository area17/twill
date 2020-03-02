<?php

namespace A17\Twill\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class Build extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twill:build';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Build Twill assets";

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
        $progressBar->setFormat("%current%/%max% [%bar%] %percent:3s%% %message%");

        $progressBar->setMessage("Installing npm dependencies...\n\n");
        $progressBar->start();

        $this->npmInstall();

        $this->info('');
        $progressBar->setMessage("Copying custom blocks...");
        $progressBar->advance();

        $this->copyBlocks();

        $this->info('');
        $progressBar->setMessage("Building assets (be patient...)\n\n");
        $progressBar->advance();

        $this->npmBuild();

        $this->info('');
        $progressBar->setMessage("Publishing assets...");
        $progressBar->advance();
        $this->call('twill:update');

        $this->info('');
        $progressBar->setMessage("Done.");
        $progressBar->finish();
    }

    /**
     * @return void
     */
    private function npmInstall()
    {
        $npmInstallProcess = new Process(['npm', 'ci'], base_path(config('twill.vendor_path')));
        $npmInstallProcess->setTty(Process::isTtySupported());
        $npmInstallProcess->mustRun();
    }

    /**
     * @return void
     */
    private function npmBuild()
    {
        $npmBuildProcess = new Process(['npm', 'run', 'build'], base_path(config('twill.vendor_path')));
        $npmBuildProcess->setTty(Process::isTtySupported());
        $npmBuildProcess->mustRun();
    }

    /**
     * @return void
     */
    private function copyBlocks()
    {
        $localCustomBlocksPath = resource_path(config('twill.block_editor.custom_vue_blocks_resource_path', 'assets/js/blocks'));
        $twillCustomBlocksPath = base_path(config('twill.vendor_path')) . '/frontend/js/components/blocks/customs';

        if (!$this->filesystem->exists($twillCustomBlocksPath)) {
            $this->filesystem->makeDirectory($twillCustomBlocksPath);
        }

        $this->filesystem->cleanDirectory($twillCustomBlocksPath);

        if (!$this->filesystem->exists($localCustomBlocksPath)) {
            $this->filesystem->makeDirectory($localCustomBlocksPath);
        }

        $this->filesystem->copyDirectory($localCustomBlocksPath, $twillCustomBlocksPath);
    }
}
