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
    protected $signature = 'twill:publish
        {--force|force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Publish Twill assets";


    /**
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();
    }

    /*
     * Executes the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $force = $this->option('force');
        $options = [
            '--provider' => 'A17\Twill\TwillServiceProvider'
        ];
        if ($force) {
            $options['--force'] = '--force';
        }

        $this->call('vendor:publish', $options);
    }
}
