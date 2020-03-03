<?php

namespace A17\Twill\Commands;

use Illuminate\Config\Repository as Config;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Composer;
use Illuminate\Support\Str;

class ListIcons extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twill:list:icons {filter? : Filter icons by name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List available icons';

    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param Filesystem $files
     * @param Composer $composer
     * @param Config $config
     */
    public function __construct(
        Filesystem $files,
        Composer $composer,
        Config $config
    ) {
        parent::__construct();

        $this->files = $files;
        $this->config = $config;
    }

    /**
     * Executes the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $icons = collect(
            $this->files->files(__DIR__ . '/../../frontend/icons')
        )->map(function ($file) {
            return [
                'name' => Str::before($file->getFilename(), '.svg'),
                'url' => route('admin.icons.show', [
                    'file' => $file->getFilename(),
                ]),
            ];
        });

        if (filled($filter = $this->argument('filter'))) {
            $icons = $icons->filter(function ($icon) use ($filter) {
                return Str::contains(
                    Str::lower($icon['name']),
                    Str::lower($filter)
                );
            });
        }

        $this->table(['Icon', 'Preview URL'], $icons->toArray());
    }
}
