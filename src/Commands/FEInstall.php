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
     */
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
        $npmInstallProcess = new Process(['npm', 'ci'], base_path(config('twill.vendor_path')));
        $npmInstallProcess->setTty(true);
        $npmInstallProcess->mustRun();
    }

    public static function checkNodeModules()
    {
        return file_exists(config('twill.vendor_path').'/node_modules');
    }
}
