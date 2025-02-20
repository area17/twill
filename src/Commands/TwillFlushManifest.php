<?php

namespace A17\Twill\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class TwillFlushManifest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twill:flush-manifest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Flush twill-manifest.json file from cache';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Cache::forget('twill-manifest');

        $this->info('Twill manifest was flushed from cache.');

        return 0;
    }
}
