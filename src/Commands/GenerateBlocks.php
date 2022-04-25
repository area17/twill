<?php

namespace A17\Twill\Commands;

use A17\Twill\Services\Blocks\Block;
use A17\Twill\Services\Blocks\BlockCollection;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Illuminate\View\Factory as ViewFactory;

class GenerateBlocks extends Command
{
    /**
     * @var string
     */
    public const NO_BLOCKS_DEFINED = 'There are no blocks defined yet. Please refer to https://twill.io/docs/#block-editor-3 in order to create blocks.';

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

    public function __construct(protected Filesystem $filesystem, protected ViewFactory $viewFactory)
    {
        parent::__construct();
    }

    /**
     * Executes the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        if (!$this->filesystem->exists($path = resource_path('views/twill/blocks'))) {
            $this->error(self::NO_BLOCKS_DEFINED);

            return;
        }

        $this->info(self::SCANNING_BLOCKS);

        $blocks = new BlockCollection();
        $blocks
            ->collect()
            ->where('compiled', true)
            ->whereIn('source', [Block::SOURCE_APP, Block::SOURCE_CUSTOM])
            ->map(function ($block): void {
                $blockName = str_replace('a17-block-', '', $block->component);
                $basename = str_replace('.blade.php', '', $block->fileName);

                $vueBlockTemplate = $this->viewFactory->make('twill.blocks.' . $basename, ['renderForBlocks' => true])->render();

                $vueBlockContent = $this->viewFactory->make('twill::blocks.builder', [
                    'render' => $this->sanitize($vueBlockTemplate),
                ])->render();

                $vueBlockPath = $this->makeDirectory(resource_path(config('twill.block_editor.custom_vue_blocks_resource_path', 'assets/js/blocks'))) . '/Block' . Str::title($blockName) . '.vue';

                $write = ! $this->filesystem->exists($vueBlockPath);

                if (! $write) {
                    $write = $this->confirm(sprintf('[%s] exists, overwrite?', $vueBlockPath), false);
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
     */
    public function makeDirectory(string $directory): string
    {
        if (! $this->filesystem->exists($directory)) {
            $this->filesystem->makeDirectory($directory, 0755, true);
        }

        return $directory;
    }

    /**
     * Sanitizes the given HTML code by removing redundant spaces and comments.
     */
    private function sanitize(string $html): ?string
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

        return preg_replace($search, $replace, Block::removeSpecialBladeTags($html));
    }
}
