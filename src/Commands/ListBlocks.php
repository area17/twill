<?php

namespace A17\Twill\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use A17\Twill\Services\Blocks\Parser as BlocksParser;

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

    public function __construct(BlocksParser $blocksParser)
    {
        parent::__construct();

        $this->blocksParser = $blocksParser;
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

        $blocks = $this->blocksParser
            ->all()
            ->reject(function ($block) use ($sourceFiltered) {
                return $sourceFiltered && !$this->option($block->source);
            })
            ->reject(function ($block) use ($typeFiltered) {
                return $this->dontPassTextFilter($block) ||
                    ($typeFiltered &&
                        !$this->option(Str::plural($block->type)));
            })
            ->map(function ($block) {
                return $this->colorize($block->list());
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
            ->keys()
            ->map(function ($key) {
                return Str::studly($key);
            });

        $this->table($headers, $blocks);
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
                ->list()
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
