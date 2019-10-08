<?php

namespace A17\Twill\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\ProcessUtils;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

class FEBuild extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twill:fe-build';

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
     * @var string
     */
    protected $packagePath = 'vendor/area17/twill';

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
        if(!FEInstall::checkNodeModules()) {
            $this->info('Twill frontend dependencies are not installed.');
            $this->info('Install it right now !');
            $this->call('twill:fe-install');
        }
        config(['twill.fe_prod' => true]);
        $this->npmBuild();
        $this->info('Publish build Twill fe assets');
        $this->call('twill:fe-publish');
    }

    /**
     * Start vue-cli-serve command
     *
     * @return void
     */
    private function npmBuild()
    {
        $npmBuildProcess = new Process(['npm', 'run', 'build'], base_path($this->packagePath));
        $npmBuildProcess->setTty(true);
        $npmBuildProcess->mustRun();
    }
}
