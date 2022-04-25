<?php

namespace A17\Twill\Commands;

class Dev extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twill:dev {--noInstall}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Hot reload Twill assets with custom Vue components/blocks";

    /*
     * Executes the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        $this->call('twill:build', [
            '--hot' => true,
            '--noInstall' => $this->option('noInstall'),
        ]);
    }
}
