<?php

namespace A17\Twill\Commands;

use File;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class Build extends Command
{
    protected $signature = 'twill:build';

    protected $description = "Build Twill assets (experimental)";

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
        $progressBar->setMessage("Copying assets...");
        $progressBar->advance();

        File::copyDirectory(base_path('vendor/area17/twill/public'), public_path());

        File::delete(public_path('hot'));

        $this->info('');
        $progressBar->setMessage("Done.");
        $progressBar->finish();
    }

    private function npmInstall()
    {
        $npmInstallProcess = new Process(['npm', 'ci'], base_path('vendor/area17/twill'));
        $npmInstallProcess->setTty(true);
        $npmInstallProcess->mustRun();
    }

    private function npmBuild()
    {
        $npmBuildProcess = new Process(['npm', 'run', 'prod'], base_path('vendor/area17/twill'));
        $npmBuildProcess->setTty(true);
        $npmBuildProcess->mustRun();
    }

    private function copyBlocks()
    {
        $localCustomBlocksPath = resource_path('assets/js/blocks');
        $twillCustomBlocksPath = base_path('vendor/area17/twill/frontend/js/components/blocks/customs');

        if (!File::exists($twillCustomBlocksPath)) {
            File::makeDirectory($twillCustomBlocksPath);
        }

        File::cleanDirectory($twillCustomBlocksPath);

        if (!File::exists($localCustomBlocksPath)) {
            File::makeDirectory($localCustomBlocksPath);
        }

        File::copyDirectory($localCustomBlocksPath, $twillCustomBlocksPath);
    }
}
