<?php

namespace A17\Twill\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\Factory as ViewFactory;

class GenerateBlocks extends Command
{
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
    protected $description = "Generate blocks as single file Vue components from blade views";

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var ViewFactory
     */
    protected $viewFactory;

    /**
     * @param Filesystem $filesystem
     * @param ViewFactory $viewFactory
     */
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
        $this->info("Starting to scan block views directory...");
        Collection::make($this->filesystem->files(resource_path('views/admin/blocks')))->each(function ($viewFile) {
            $blockName = $viewFile->getBasename('.blade.php');

            $vueBlockTemplate = $this->viewFactory->make('admin.blocks.' . $blockName, ['renderForBlocks' => true])->render();

            $vueBlockContent = $this->viewFactory->make('twill::blocks.builder', [
                'render' => $this->sanitize($vueBlockTemplate),
            ])->render();

            $vueBlockPath = resource_path('assets/js/blocks/') . 'Block' . Str::title($blockName) . '.vue';

            $this->filesystem->put($vueBlockPath, $vueBlockContent);

            $this->info("Block " . Str::title($blockName) . " generated successfully");
        });

        $this->info("All blocks have been generated!");
    }

    /**
     * Sanitizes the given HTML code by removing redundant spaces and comments.
     *
     * @param string $html
     * @return string
     */
    private function sanitize($html)
    {
        $search = array(
            '/\>[^\S ]+/s', // strip whitespaces after tags, except space
            '/[^\S ]+\</s', // strip whitespaces before tags, except space
            '/(\s)+/s', // shorten multiple whitespace sequences
            '/<!--(.|\s)*?-->/', // Remove HTML comments
        );

        $replace = array(
            '>',
            '<',
            '\\1',
            '',
        );

        return preg_replace($search, $replace, $html);
    }

}
