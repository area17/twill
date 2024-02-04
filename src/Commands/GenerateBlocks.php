<?php

namespace A17\Twill\Commands;

use A17\Twill\Facades\TwillBlocks;
use A17\Twill\Services\Blocks\Block;
use A17\Twill\Services\Blocks\BlockCollection;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Illuminate\View\Factory as ViewFactory;

class GenerateBlocks extends Command
{
    /**
     * @var string
     */
    public const NO_BLOCKS_DEFINED = 'There are no blocks defined yet. Please refer to https://twillcms.com/block-editor/index.html in order to create blocks.';

    /**
     * @var string
     */
    public const SCANNING_BLOCKS = 'Starting to scan block views directory...';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twill:blocks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate blocks as single file Vue components from blade views';

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var ViewFactory
     */
    protected $viewFactory;

    public function __construct(Filesystem $filesystem, ViewFactory $viewFactory)
    {
        parent::__construct();

        $this->filesystem = $filesystem;
        $this->viewFactory = $viewFactory;
    }

    /**
     * Executes the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (!$this->filesystem->exists($path = resource_path('views/twill/blocks'))) {
            $this->error(self::NO_BLOCKS_DEFINED);

            return;
        }

        $this->info(self::SCANNING_BLOCKS);

        $blocks = TwillBlocks::getBlockCollection();
        $blocks
            ->collect()
            ->where('compiled', true)
            ->whereIn('source', [Block::SOURCE_APP, Block::SOURCE_CUSTOM])
            ->map(function ($block) {
                $blockName = str_replace('a17-block-', '', $block->component);
                $basename = str_replace('.blade.php', '', $block->fileName);

                View::share('TwillUntilConsumed', ['renderForBlocks' => true]);
                $vueBlockTemplate = $this->viewFactory->make('twill.blocks.' . $basename, ['renderForBlocks' => true])->render();
                View::share('TwillUntilConsumed', []);

                $vueBlockContent = $this->viewFactory->make('twill::blocks.builder', [
                    'render' => $this->sanitize($vueBlockTemplate),
                ])->render();

                $vueBlockPath = $this->makeDirectory(resource_path(config('twill.block_editor.custom_vue_blocks_resource_path', 'assets/js/blocks'))) . '/Block' . Str::title($blockName) . '.vue';

                $write = ! $this->filesystem->exists($vueBlockPath);

                if (! $write) {
                    $write = $this->confirm("[$vueBlockPath] exists, overwrite?", false);
                }

                if ($write) {
                    $this->filesystem->put($vueBlockPath, $vueBlockContent);
                    $this->info('Block ' . Str::title($blockName) . ' generated successfully');
                } else {
                    $this->info('Skipping block ' . Str::title($blockName) . '.');
                }
            });

        $this->info('All blocks have been generated!');
    }

    /**
     * Recursively make a directory.
     *
     * @param string $directory
     * @return string
     */
    public function makeDirectory($directory)
    {
        if (! $this->filesystem->exists($directory)) {
            $this->filesystem->makeDirectory($directory, 0755, true);
        }

        return $directory;
    }

    /**
     * Sanitizes the given HTML code by removing redundant spaces and comments.
     *
     * @param string $html
     * @return string
     */
    private function sanitize($html)
    {
        $search = [
            '/\>[^\S ]+/s', // strip whitespaces after tags, except space
            '/[^\S ]+\</s', // strip whitespaces before tags, except space
            '/(\s)+/s', // shorten multiple whitespace sequences
            '/<!--(.|\s)*?-->/', // Remove HTML comments
        ];

        $replace = [
            '>',
            '<',
            '\\1',
            '',
        ];

        return preg_replace($search, $replace, $this->removeSpecialBladeTags($html));
    }

    /**
     * @param $contents
     * @return string
     */
    private function removeSpecialBladeTags($contents)
    {
        return preg_replace([
            "/@twillProp.*\(" . Block::PREG_REPLACE_INNER . "\)/sU",
            "/@twillBlock.*\(" . Block::PREG_REPLACE_INNER . "\)/sU",
            "/@twillRepeater.*\(" . Block::PREG_REPLACE_INNER . "\)/sU",
        ], '', $contents);
    }
}
