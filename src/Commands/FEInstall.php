<?php

namespace A17\Twill\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class FEInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twill:fe-install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Install dependencies to build Twill frontend";

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
    public function handle()
    {
        $progressBar = $this->output->createProgressBar(1);
        $progressBar->setFormat("%current%/%max% [%bar%] %percent:3s%% %message%");

        $progressBar->setMessage("Installing npm dependencies...\n\n");
        $progressBar->start();

        $this->npmInstall();

        $progressBar->setMessage("Frontend dependencies are installed");
        $progressBar->finish();
    }

    /**
     * @return void
     */
    private function npmInstall()
    {
        $npmInstallProcess = new Process(['npm', 'ci'], base_path('vendor/area17/twill'));
        $npmInstallProcess->setTty(true);
        $npmInstallProcess->mustRun();
    }

    /**
     * @return void
     */
    private function npmBuild()
    {
        $npmBuildProcess = new Process(['npm', 'run', 'prod'], base_path('vendor/area17/twill'));
        $npmBuildProcess->setTty(true);
        $npmBuildProcess->mustRun();
    }

    /**
     * @return void
     */
    private function copyBlocks()
    {
        $localCustomBlocksPath = resource_path('assets/js/blocks');
        $twillCustomBlocksPath = base_path('vendor/area17/twill/frontend/js/components/blocks/customs');

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
