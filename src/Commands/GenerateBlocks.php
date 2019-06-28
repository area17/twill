<?php

namespace A17\Twill\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

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
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();

        $this->filesystem = $filesystem;
    }

    /**
     * Executes the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("Starting to scan block views directory...");
        collect($this->filesystem->files(resource_path('views/admin/blocks')))->each(function ($viewFile) {
            $blockName = $viewFile->getBasename('.blade.php');

            $vueBlockTemplate = view('admin.blocks.' . $blockName, ['renderForBlocks' => true])->render();

            $vueBlockContent = view('twill::blocks.builder', [
                'render' => $this->sanitize($vueBlockTemplate),
            ])->render();

            $vueBlockPath = resource_path('assets/js/blocks/') . 'Block' . title_case($blockName) . '.vue';

            $this->filesystem->put($vueBlockPath, $vueBlockContent);

            $this->info("Block " . title_case($blockName) . " generated successfully");
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
