<?php

namespace A17\Twill\Commands;

use A17\Twill\Commands\Traits\ExecutesInTwillDir;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class Build extends Command
{
    use ExecutesInTwillDir;

    /**
     * The name and signature of the console command.
     *
     * @var string
     *
     * forTesting is only for when the test suite needs to run (on ci) as there are no build dependencies there.
     * This will result in a git diff.
     */
    protected $signature = 'twill:build {--install} {--hot} {--watch} {--copyOnly} {--customComponentsSource=} {--forTesting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build Twill assets with custom Vue components/blocks';

    public function __construct(public Filesystem $filesystem)
    {
        parent::__construct();
    }

    /*
     * Executes the console command.
     */
    public function handle(): void
    {
        if ($this->option('copyOnly')) {
            $this->copyCustoms();

            return;
        }

        $this->fullBuild();
    }

    private function fullBuild(): void
    {
        $progressBar = $this->output->createProgressBar(5);
        $progressBar->setFormat('%current%/%max% [%bar%] %message%');

        // Check if the node_modules folder is missing, if it is not there we enforce the installation.
        $npmInstall = !file_exists(__DIR__ . '/../../node_modules') || $this->option('install');

        $progressBar->setMessage(($npmInstall ? 'Installing' : 'Reusing') . " npm dependencies...\n\n");

        $progressBar->start();

        if ($npmInstall) {
            $this->runProcessInTwill(['npm', 'ci']);
        }

        $this->info('');
        $progressBar->setMessage("Copying custom blocks...\n\n");
        $progressBar->advance();

        $this->copyBlocks();
        sleep(1);

        $this->info('');
        if (!$this->option('customComponentsSource')) {
            $progressBar->setMessage("Copying custom components...\n\n");
        } else {
            $progressBar->setMessage("Loading components from custom directory...\n\n");
        }
        $progressBar->advance();

        $this->copyComponents();
        $progressBar->setMessage("Copying icons...\n\n");
        $this->copyIcons();
        sleep(1);

        $this->copyVendorComponents();
        sleep(1);

        $this->info('');
        $progressBar->setMessage("Building assets...\n\n");
        $progressBar->advance();

        $env = [];
        if ($this->option('customComponentsSource')) {
            $progressBar->setMessage("Using custom components from {$this->option('customComponentsSource')} ...\n\n");
            if (str_contains($this->option('customComponentsSource'), '..')) {
                $this->error('customComponentsSource must be an absolute path');
                exit(1);
            }
            $env = ['VUE_APP_CUSTOM_COMPONENTS_PATH' => $this->option('customComponentsSource')];
        }

        if ($this->option('hot')) {
            $this->startWatcher(resource_path('assets/js/**/*.vue'), 'php artisan twill:build --copyOnly');
            $this->runProcessInTwill(
                command: ['npm', 'run', 'serve', '--', "--port={$this->getDevPort()}"],
                disableTimeout: true,
                env: $env
            );
        } elseif ($this->option('watch')) {
            $this->startWatcher(resource_path('assets/js/**/*.vue'), 'php artisan twill:build --copyOnly');
            $this->runProcessInTwill(
                command: ['npm', 'run', 'watch'],
                disableTimeout: true,
                env: $env
            );
        } else {
            $this->runProcessInTwill(
                command: ['npm', 'run', 'build'],
                env: $env
            );

            $this->info('');
            $progressBar->setMessage("Publishing assets...\n\n");
            $progressBar->advance();

            $this->call('twill:update', ['--fromBuild' => true]);

            $this->info('');
            $progressBar->setMessage('Done.');
            $progressBar->finish();
        }

        if ($this->option('forTesting')) {
            $this->executeInTwillDir('rm -Rf twill-assets/assets && cp -Rf dist/assets twill-assets/assets');
        }
    }

    private function getDevPort(): string
    {
        preg_match('#^.*:(\d+)#', config('twill.dev_mode_url'), $matches);

        return $matches[1] ?? '8080';
    }

    private function startWatcher(string $pattern, string $command): void
    {
        if (empty($this->filesystem->glob($pattern))) {
            return;
        }

        $chokidarPath = base_path(config('twill.vendor_path')) . '/node_modules/.bin/chokidar';
        $chokidarCommand = [$chokidarPath, $pattern, '-c', $command];

        if ($this->filesystem->exists($chokidarPath)) {
            $process = new Process($chokidarCommand, base_path());
            $process->setTty(Process::isTtySupported());
            $process->setTimeout(null);

            try {
                $process->start();
            } catch (\Exception $exception) {
                $this->warn("Could not start the chokidar watcher ({$exception->getMessage()})\n");
            }
        } else {
            $this->warn(
                "The `chokidar-cli` package was not found. It is required to watch custom blocks & components in development. You can install it by running:\n"
            );
            $this->warn("    php artisan twill:dev\n");
            $this->warn("with the `--install` option.\n");
            sleep(2);
        }
    }

    private function runProcessInTwill(array $command, bool $disableTimeout = false, array $env = []): void
    {
        $process = new Process($command, base_path(config('twill.vendor_path')));
        $process->setTty(Process::isTtySupported());

        $process->setEnv($env);

        if ($disableTimeout) {
            $process->setTimeout(null);
        } else {
            $process->setTimeout(config('twill.build_timeout', 300));
        }

        $process->mustRun();
    }

    private function copyCustoms(): void
    {
        $this->info('Copying custom blocks & components...');
        $this->copyBlocks();
        $this->copyComponents();
        $this->info('Done.');
    }

    private function copyBlocks(): void
    {
        $localCustomBlocksPath = resource_path(
            config('twill.block_editor.custom_vue_blocks_resource_path', 'assets/js/blocks')
        );
        $twillCustomBlocksPath = base_path(config('twill.vendor_path')) . '/frontend/js/components/blocks/customs';

        if (!$this->filesystem->exists($twillCustomBlocksPath)) {
            $this->filesystem->makeDirectory($twillCustomBlocksPath, 0755, true);
        }

        $this->filesystem->cleanDirectory($twillCustomBlocksPath);

        if ($this->filesystem->exists($localCustomBlocksPath)) {
            $this->filesystem->copyDirectory($localCustomBlocksPath, $twillCustomBlocksPath);
        }
    }

    private function copyComponents(): void
    {
        $localCustomComponentsPath = resource_path(
            config('twill.custom_components_resource_path', 'assets/js/components')
        );
        $twillCustomComponentsPath = base_path(config('twill.vendor_path')) . '/frontend/js/components/customs';

        if (!$this->filesystem->exists($twillCustomComponentsPath)) {
            $this->filesystem->makeDirectory($twillCustomComponentsPath, 0755, true);
        }

        $this->filesystem->cleanDirectory($twillCustomComponentsPath);
        $this->filesystem->put($twillCustomComponentsPath . '/.keep', '');

        if ($this->filesystem->exists($localCustomComponentsPath)) {
            $this->filesystem->copyDirectory($localCustomComponentsPath, $twillCustomComponentsPath);
        }
    }

    private function copyIcons(): void
    {
        $targetDirectory = base_path('vendor/area17/twill/frontend/icons-custom');
        $originalIcons = config('twill.block_editor.core_icons');

        if (!file_exists($targetDirectory)) {
            mkdir($targetDirectory);
        }

        foreach (config('twill.block_editor.directories.source.icons') as $iconDirectory) {
            // We do not want to process original icons.
            if ($iconDirectory !== $originalIcons) {
                foreach (glob($iconDirectory . DIRECTORY_SEPARATOR . '*.svg') as $svg) {
                    $exploded = explode(DIRECTORY_SEPARATOR, $svg);
                    $fileName = array_pop($exploded);
                    copy($svg, $targetDirectory . DIRECTORY_SEPARATOR . $fileName);
                }
            }
        }
    }

    private function copyVendorComponents(): void
    {
        $localVendorComponentsPath = resource_path(
            config('twill.vendor_components_resource_path', 'assets/vendor/js/components')
        );
        $twillVendorComponentsPath = base_path(config('twill.vendor_path')) . '/frontend/js/components/customs-vendor';

        if (!$this->filesystem->exists($twillVendorComponentsPath)) {
            $this->filesystem->makeDirectory($twillVendorComponentsPath, 0755, true);
        }

        $this->filesystem->cleanDirectory($twillVendorComponentsPath);
        $this->filesystem->put($twillVendorComponentsPath . '/.keep', '');

        if ($this->filesystem->exists($localVendorComponentsPath)) {
            $this->filesystem->copyDirectory($localVendorComponentsPath, $twillVendorComponentsPath);
        }
    }
}
