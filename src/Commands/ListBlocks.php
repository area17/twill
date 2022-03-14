<?php

namespace A17\Twill\Commands;

use A17\Twill\Services\Blocks\Block;
use A17\Twill\Services\Blocks\BlockCollection;
use Illuminate\Support\Str;

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
        '{--r|--repeaters : List only repeaters}' .
        '{--s|--short : List with a shorter amount of columns}';

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
    protected $blockCollection;

    public function __construct(BlockCollection $blockCollection)
    {
        parent::__construct();

        $this->blockCollection = $blockCollection;
    }

    protected function displayMissingDirectories(): void
    {
        $this->blockCollection
            ->getMissingDirectories()
            ->each(function ($directory) {
                $this->error("Directory not found: {$directory}");
            });
    }

    /**
     * @param \Illuminate\Support\Collection $blockCollection
     * @return mixed
     */
    protected function generateHeaders($blockCollection)
    {
        return $blockCollection
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

        return $this->blockCollection
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
                $data = $this->colorize(
                    $this->option('short') ? $block->toShortList() : $block->toList()
                );
                // We do not render this.
                unset($data['rules'], $data['rulesForTranslatedFields']);
                return $data;
            })
            ->sortBy(function ($block) {
                return [$block['group'], $block['title']];
            });
    }

    /**
     * @return \A17\Twill\Services\Blocks\BlockCollection
     */
    public function getBlockCollection()
    {
        return $this->blockCollection;
    }

    /**
     * Executes the console command.
     *
     * @return void
     */
    public function handle()
    {
        $blockCollection = $this->getBlocks();

        $this->displayMissingDirectories();

        if ($blockCollection->isEmpty()) {
            $this->error('No blocks found.');

            return;
        }

        $this->table(
            $this->generateHeaders($blockCollection),
            $blockCollection->toArray()
        );
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
                    if (is_array($element)) {
                        return false;
                    }
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
