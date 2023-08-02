<?php

namespace A17\Twill\Commands;

class Dev extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twill:dev {--install} {--customComponentsSource=}';

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
    public function handle()
    {
        $options = [
            '--hot' => true,
            '--install' => $this->option('install'),
        ];

        if ($this->option('customComponentsSource')) {
            $options['--customComponentsSource'] = $this->option('customComponentsSource');
        }

        $this->call('twill:build', $options);
    }
}
