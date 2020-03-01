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
        'twill:list:blocks ' .
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
     * Executes the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $sourceFiltered =
            $this->option('twill') ||
            $this->option('custom') ||
            $this->option('app');

        $typeFiltered = $this->option('blocks') || $this->option('repeaters');

        $blocks = $this->blocksParser
            ->all()
            ->reject(function ($block) use ($sourceFiltered) {
                return $sourceFiltered && !$this->option($block['source']);
            })
            ->reject(function ($block) use ($typeFiltered) {
                return $typeFiltered && !$this->option(Str::plural($block['type']));
            });

        $headers = $blocks
            ->first()
            ->keys()
            ->map(function ($key) {
                return Str::studly($key);
            });

        $this->table($headers, $blocks);
    }
}
