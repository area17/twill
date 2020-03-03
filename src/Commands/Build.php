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
    protected $signature = 'twill:build {--noInstall} {--hot} {--watch}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Build Twill assets with custom blocks";

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
        $progressBar = $this->output->createProgressBar(5);
        $progressBar->setFormat("%current%/%max% [%bar%] %message%");

        $npmInstall = !$this->option('noInstall');

        $progressBar->setMessage(($npmInstall ? "Installing" : "Reusing") . " npm dependencies...\n\n");

        $progressBar->start();

        if ($npmInstall) {
            $this->runProcessInTwill(['npm', 'ci']);
        } else {
            sleep(1);
        }

        $this->info('');
        $progressBar->setMessage("Copying custom blocks...\n\n");
        $progressBar->advance();

        $this->copyBlocks();
        sleep(1);

        $this->info('');
        $progressBar->setMessage("Copying custom components...\n\n");
        $progressBar->advance();

        $this->copyComponents();
        sleep(1);

        $this->info('');
        $progressBar->setMessage("Building assets...\n\n");
        $progressBar->advance();

        if ($this->option('hot')) {
            $this->runProcessInTwill(['npm', 'run', 'serve'], true);
        } elseif ($this->option('watch')) {
            $this->runProcessInTwill(['npm', 'run', 'watch'], true);
        } else {
            $this->runProcessInTwill(['npm', 'run', 'build']);

            $this->info('');
            $progressBar->setMessage("Publishing assets...\n\n");
            $progressBar->advance();
            $this->call('twill:update');

            $this->info('');
            $progressBar->setMessage("Done.");
            $progressBar->finish();
        }
    }

    /**
     * @return void
     */
    private function runProcessInTwill(array $command, $disableTimeout = false)
    {
        $process = new Process($command, base_path(config('twill.vendor_path')));
        $process->setTty(Process::isTtySupported());

        if ($disableTimeout) {
            $process->setTimeout(null);
        }

        $process->mustRun();
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

    /**
     * @return void
     */
    private function copyComponents()
    {
        $localCustomComponentsPath = resource_path(config('twill.custom_components_resource_path', 'assets/js/components'));
        $twillCustomComponentsPath = base_path(config('twill.vendor_path')) . '/frontend/js/components/customs';

        if (!$this->filesystem->exists($twillCustomComponentsPath)) {
            $this->filesystem->makeDirectory($twillCustomComponentsPath);
        }

        $this->filesystem->cleanDirectory($twillCustomComponentsPath);

        if (!$this->filesystem->exists($localCustomComponentsPath)) {
            $this->filesystem->makeDirectory($localCustomComponentsPath);
        }

        $this->filesystem->copyDirectory($localCustomComponentsPath, $twillCustomComponentsPath);
    }
}
