<?php

namespace A17\Twill\Commands;

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
        '{base? : Block on which the new block should be based.}' .
        '{icon? : Icon for the new block. List icons using the twill:list:icons command.}';

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

    public function getBlockMaker(): \A17\Twill\Services\Blocks\BlockMaker
    {
        return $this->blockMaker;
    }

    /**
     * Executes the console command.
     *
     * @return mixed
     * @throws \Exception
     */
    public function handle()
    {
        $generateView = $this->confirm('Should we also generate a view file for rendering the block?');

        $this->blockMaker
            ->setCommand($this)
            ->make(
                $this->argument('name'),
                $this->argument('base') ?? 'text',
                $this->argument('icon') ?? 'text',
                $generateView
            );

        return parent::handle();
    }
}
