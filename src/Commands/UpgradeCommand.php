<?php

namespace A17\Twill\Commands;

use Composer\XdebugHandler\Process;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UpgradeCommand extends Command
{
    protected $signature = 'twill:upgrade';

    protected $description = 'Performs the required changes to upgrade twill to version 3.x';

    /**
     * @var \Illuminate\Filesystem\FilesystemAdapter
     */
    protected $fsAsStorage;

    public function handle()
    {
        $this->fsAsStorage = Storage::build([
            'driver' => 'local',
            'root' => app()->basePath(),
        ]);

        if (!$this->fsAsStorage->exists('vendor/bin/rector')) {
            $this->error('Rector is not installed, please install it using:');
            $this->info('composer require rector/rector --dev');
            $this->line('Then rerun the command.');
            exit(1);
        }

        if (config('app.env') === 'production') {
            $this->error('Do not run this on production.');
            exit(1);
        }

        $this->info('This command will refactor code in your codebase.');
        $this->info(
            'Before you start the upgrade, please make sure you have a backup. Do not run this command on production!'
        );
        if (!$this->confirm('Are you sure you want to start the upgrade?', false)) {
            $this->error('Cancelled');
            exit(1);
        }

        $this->moveRoutesFile();
        $this->moveResourcesAdminFolder();
        $this->moveControllerAdminDirectories();

        $this->dumpAutoloader();
        $this->runRector();
    }

    protected function moveRoutesFile(): void
    {
        if (!$this->fsAsStorage->exists('routes/admin.php')) {
            $this->warn('Not moving routes/admin.php, file not present.');
            return;
        }

        $this->info('Moving routes/admin.php to routes/twill.php');
        $this->fsAsStorage->move('routes/admin.php', 'routes/twill.php');
        $this->newLine();
    }

    protected function moveResourcesAdminFolder(): void
    {
        if ($this->fsAsStorage->exists('resources/views/twill')) {
            $this->warn('Not moving resources/views/admin, resources/views/twill already exists.');
            return;
        }

        $this->info('Moving resources/views/admin/* to resources/views/twill*');
        $this->fsAsStorage->move('resources/views/admin', 'resources/views/twill');
        $this->newLine();
    }

    protected function moveControllerAdminDirectories(): void
    {
        if ($this->fsAsStorage->exists('app/Http/Controllers/Twill')) {
            $this->warn('Not moving app/Http/Controllers/Admin, app/Http/Controllers/Twill already exists.');
            return;
        }

        $this->info('Moving app/Http/Controllers/Admin to app/Http/Controllers/Twill');
        $this->fsAsStorage->move('app/Http/Controllers/Admin', 'app/Http/Controllers/Twill');
        $this->info('Moving app/Http/Requests/Admin to app/Http/Requests/Twill');
        $this->fsAsStorage->move('app/Http/Requests/Admin', 'app/Http/Requests/Twill');
        $this->newLine();
    }

    protected function dumpAutoloader(): void
    {
        sleep(1);
        $this->info('Dumping composer autoloader');
        $process = new \Symfony\Component\Process\Process(
            ['composer', 'dump-autoload'],
            null,
            null,
            null,
            null
        );
        $process->run();

        if (!$process->isSuccessful()) {
            $this->error('Failed running composer dump-autoload');
            $this->error($process->getOutput());
            $this->error($process->getErrorOutput());
        }
    }

    protected function runRector(): void
    {
        $this->info('Running rector refactorings');

        $process = new \Symfony\Component\Process\Process(
            ['php', 'vendor/bin/rector', 'process', '--config=vendor/area17/twill/rector-upgrade.php'],
            null,
            null,
            null,
            null
        );
        $process->run();

        if (!$process->isSuccessful()) {
            $this->error('Failed running rector');
            $this->error($process->getOutput());
            $this->error($process->getErrorOutput());
            exit(1);
        }

        $this->info('Successfully ran refactorings');
        $this->info($process->getOutput());
    }
}
