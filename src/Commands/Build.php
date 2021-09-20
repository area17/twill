<?php

namespace A17\Twill\Commands;

use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class Build extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twill:build {--noInstall} {--hot} {--watch} {--copyOnly}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Build Twill assets with custom Vue components/blocks";

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
        if ($this->option("copyOnly")) {
            return $this->copyCustoms();
        }

        return $this->fullBuild();
    }

    /*
     * @return void
     */
    private function fullBuild()
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
            $this->startWatcher(resource_path('assets/js/**/*.vue'), 'php artisan twill:build --copyOnly');
            $this->runProcessInTwill(['npm', 'run', 'serve', '--', "--port={$this->getDevPort()}"], true);
        } elseif ($this->option('watch')) {
            $this->startWatcher(resource_path('assets/js/**/*.vue'), 'php artisan twill:build --copyOnly');
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
     * @return string
     */
    private function getDevPort()
    {
        preg_match('/^.*:(\d+)/', config('twill.dev_mode_url'), $matches);

        return $matches[1] ?? '8080';
    }

    /**
     * @return void
     */
    private function startWatcher($pattern, $command)
    {
        if (empty($this->filesystem->glob($pattern))) {
            return;
        }

        $chokidarPath = base_path(config('twill.vendor_path')) . '/node_modules/.bin/chokidar';
        $chokidarCommand = [$chokidarPath, $pattern, "-c", $command];

        if ($this->filesystem->exists($chokidarPath)) {
            $process = new Process($chokidarCommand, base_path());
            $process->setTty(Process::isTtySupported());
            $process->setTimeout(null);

            try {
                $process->start();
            } catch(\Exception $e) {
                $this->warn("Could not start the chokidar watcher ({$e->getMessage()})\n");
            }
        } else {
            $this->warn("The `chokidar-cli` package was not found. It is required to watch custom blocks & components in development. You can install it by running:\n");
            $this->warn("    php artisan twill:dev\n");
            $this->warn("without the `--noInstall` option.\n");
            sleep(2);
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
        } else {
            $process->setTimeout(config('twill.build_timeout', 300));
        }

        $process->mustRun();
    }

    /*
     * @return void
     */
    private function copyCustoms()
    {
        $this->info("Copying custom blocks & components...");
        $this->copyBlocks();
        $this->copyComponents();
        $this->info("Done.");
    }

    /**
     * @return void
     */
    private function copyBlocks()
    {
        $localCustomBlocksPath = resource_path(config('twill.block_editor.custom_vue_blocks_resource_path', 'assets/js/blocks'));
        $twillCustomBlocksPath = base_path(config('twill.vendor_path')) . '/frontend/js/components/blocks/customs';

        if (!$this->filesystem->exists($twillCustomBlocksPath)) {
            $this->filesystem->makeDirectory($twillCustomBlocksPath, 0755, true);
        }

        $this->filesystem->cleanDirectory($twillCustomBlocksPath);

        if ($this->filesystem->exists($localCustomBlocksPath)) {
            $this->filesystem->copyDirectory($localCustomBlocksPath, $twillCustomBlocksPath);
        }
    }

    /**
     * @return void
     */
    private function copyComponents()
    {
        $localCustomComponentsPath = resource_path(config('twill.custom_components_resource_path', 'assets/js/components'));
        $twillCustomComponentsPath = base_path(config('twill.vendor_path')) . '/frontend/js/components/customs';

        if (!$this->filesystem->exists($twillCustomComponentsPath)) {
            $this->filesystem->makeDirectory($twillCustomComponentsPath, 0755, true);
        }

        $this->filesystem->cleanDirectory($twillCustomComponentsPath);

        if ($this->filesystem->exists($localCustomComponentsPath)) {
            $this->filesystem->copyDirectory($localCustomComponentsPath, $twillCustomComponentsPath);
        }
    }
}
