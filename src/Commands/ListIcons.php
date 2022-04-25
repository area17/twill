<?php

namespace A17\Twill\Commands;

use Illuminate\Config\Repository as Config;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\Finder\SplFileInfo;

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

    public function __construct(protected Filesystem $files, protected Config $config)
    {
        parent::__construct();
    }

    private function isAllowed($icon): bool
    {
        if (filled($filter = $this->argument('filter'))) {
            return Str::contains(
                Str::lower($icon['name']),
                Str::lower($filter)
            );
        }
        return !in_array($icon['name'] . '.svg', config('twill.internal_icons'));
    }

    protected function getIconList(): \Illuminate\Support\Collection
    {
        return collect(
            config('twill.block_editor.directories.source.icons')
        )->reduce(function (Collection $keep, $path): \Illuminate\Support\Collection {
            if (! $this->files->exists($path)) {
                $this->error(sprintf('Directory not found: %s', $path));

                return $keep;
            }

            $files = collect($this->files->files($path))->map(function (
                SplFileInfo $file
            ): array {
                return [
                    'name' => Str::before($file->getFilename(), '.svg'),
                    'url' => route('twill.icons.show', [
                        'file' => $file->getFilename(),
                    ]),
                ];
            });

            return $keep->merge($files);
        }, collect());
    }

    /**
     * Executes the console command.
     */
    public function handle()
    {
        $icons = $this->getIconList()->filter(function ($icon): bool {
            return $this->isAllowed($icon);
        });

        $this->table(['Icon', 'Preview URL'], $icons->toArray());
        $this->info('All icons viewable at: ' . route('twill.icons.index'));

        return parent::handle();
    }
}
