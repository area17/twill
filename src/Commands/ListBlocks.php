<?php

namespace A17\Twill\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
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

    /**
     * @return \A17\Twill\Services\Blocks\BlockCollection
     */
    protected function getBlocks()
    {
        $sourceFiltered =
            $this->option('twill') ||
            $this->option('custom') ||
            $this->option('app');

        $typeFiltered = $this->option('blocks') || $this->option('repeaters');

        $blocks = $this->blocks
            ->reject(function ($block) use ($sourceFiltered) {
                return $sourceFiltered && !$this->option($block->source);
            })
            ->reject(function ($block) use ($typeFiltered) {
                return $this->dontPassTextFilter($block) ||
                    ($typeFiltered &&
                        !$this->option(Str::plural($block->type)));
            })
            ->map(function ($block) {
                return $this->colorize($block->export());
            })
            ->sortBy('title');

        return $blocks;
    }

    /**
     * Executes the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $blocks = $this->getBlocks();

        if ($blocks->isEmpty()) {
            $this->error('No blocks found.');

            return;
        }

        $headers = $blocks
            ->first()
            ->export()
            ->keys()
            ->map(function ($key) {
                return Str::studly($key);
            })->toArray();

        $this->table($headers, $blocks->toArray());
    }

    public function colorize($block)
    {
        $block['type'] =
            $block['type'] === 'repeater'
                ? $block['type']
                : "<fg=yellow>{$block['type']}</>";

        return $block;
    }

    public function dontPassTextFilter($block)
    {
        if (filled($filter = $this->argument('filter'))) {
            return !$block
                ->export()
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
