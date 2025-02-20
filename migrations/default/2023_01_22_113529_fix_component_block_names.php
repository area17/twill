<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        $blockList = \A17\Twill\Facades\TwillBlocks::getAll();

        $mapping = [];

        foreach ($blockList as $block) {
            if ($block->componentClass) {
                $mapping[$block->title] = $block->name;
            }
        }

        \A17\Twill\Models\Block::each(function (\A17\Twill\Models\Block $block) use ($mapping) {
            if (isset($mapping[$block->type])) {
                $block->type = $mapping[$block->type];
                $block->save();
            }
        });
    }
};
