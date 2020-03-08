<?php

namespace A17\Twill\Commands;

use Illuminate\Console\Command;
use A17\Twill\Services\Blocks\BlockMaker;

class BlockMake extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature =
        'twill:make:block ' .
        '{name : Name of the new block.} ' .
        '{base : Block on which it should be based on.}' .
        '{icon : Icon to be used on the new block. List icons using the twill:list:icons command.}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new block';

    /**
     * @var \A17\Twill\Services\Blocks\BlockMaker
     */
    protected $blockMaker;

    /**
     * @param \A17\Twill\Services\Blocks\BlockMaker
     */
    public function __construct(BlockMaker $blockMaker)
    {
        parent::__construct();

        $this->blockMaker = $blockMaker;
    }

    /**
     * Executes the console command.
     *
     * @return mixed
     * @throws \Exception
     */
    public function handle()
    {
        return $this->blockMaker
            ->setCommand($this)
            ->make(
                $this->argument('name'),
                $this->argument('base'),
                $this->argument('icon')
            );
    }
}
