<?php

namespace A17\Twill\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use A17\Twill\Services\Blocks\Block;
use A17\Twill\Services\Blocks\BlockCollection;

class ListBlocks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature =
        'twill:list:blocks {filter?}' .
        '{--t|--twill : List only Twill\'s internal blocks} ' .
        '{--c|--custom : List only user custom blocks} ' .
        '{--a|--app : List only legacy application blocks}' .
        '{--b|--blocks : List only blocks}' .
        '{--r|--repeaters : List only repeaters}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all available Twill blocks';

    /**
     * Blocks collection.
     *
     * @var BlockCollection
     */
    protected $blocks;

    public function __construct(BlockCollection $blocks)
    {
        parent::__construct();

        $this->blocks = $blocks;
    }

    protected function displayMissingDirectories(): void
    {
        $this->blocks->getMissingDirectories()->each(function ($directory) {
            $this->error("Directory not found: {$directory}");
        });
    }

    /**
     * @param \Illuminate\Support\Collection $blocks
     * @return mixed
     */
    protected function generateHeaders($blocks)
    {
        return $blocks
            ->first()
            ->keys()
            ->map(function ($key) {
                return Str::studly($key);
            })
            ->toArray();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    protected function getBlocks()
    {
        $sourceFiltered =
            $this->option('twill') ||
            $this->option('custom') ||
            $this->option('app');

        $typeFiltered = $this->option('blocks') || $this->option('repeaters');

        return $this->blocks
            ->collect()
            ->reject(function (Block $block) use ($sourceFiltered) {
                return $sourceFiltered && !$this->option($block->source);
            })
            ->reject(function (Block $block) use ($typeFiltered) {
                return $this->dontPassTextFilter($block) ||
                    ($typeFiltered &&
                        !$this->option(Str::plural($block->type)));
            })
            ->map(function (Block $block) {
                return $this->colorize($block->toList());
            })
            ->sortBy('title');
    }

    /**
     * Executes the console command.
     *
     * @return void
     */
    public function handle()
    {
        $blocks = $this->getBlocks();

        $this->displayMissingDirectories();

        if ($blocks->isEmpty()) {
            $this->error('No blocks found.');

            return;
        }

        $this->table($this->generateHeaders($blocks), $blocks->toArray());
    }

    /**
     * @param $block
     * @return mixed
     */
    public function colorize($block)
    {
        $color = $block['type'] === 'repeater' ? 'green' : 'yellow';

        $block['type'] = "<fg={$color}>{$block['type']}</>";

        return $block;
    }

    /**
     * @param \A17\Twill\Services\Blocks\Block $block
     * @return bool
     */
    public function dontPassTextFilter(Block $block)
    {
        if (filled($filter = $this->argument('filter'))) {
            return !$block
                ->toList()
                ->reduce(function ($keep, $element) use ($filter) {
                    return $keep ||
                        Str::contains(
                            Str::lower($element),
                            Str::lower($filter)
                        );
                }, false);
        }

        return false;
    }
}
