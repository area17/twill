<?php

namespace A17\Twill\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use A17\Twill\Services\Blocks\Block;
use Illuminate\Filesystem\Filesystem;
use A17\Twill\Services\Blocks\BlockCollection;

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
     * @var Filesystem
     */
    protected $files;

    /**
     * @param Filesystem $files
     * @param BlockCollection $blockCollection
     */
    public function __construct(
        Filesystem $files,
        BlockCollection $blockCollection
    ) {
        parent::__construct();

        $this->files = $files;

        $this->blockCollection = $blockCollection;
    }

    /**
     * Executes the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $blockName = $this->argument('name');
        $baseName = $this->argument('base');
        $iconName = $this->argument('icon');

        if (blank($blockStub = $this->getBlockByName($baseName))) {
            $this->error("Block '{$baseName}' doesn't exists.");

            return;
        }

        if (blank($icon = $this->getIconFile($iconName))) {
            $this->error("Icon '{$iconName}' doesn't exists.");

            return;
        }

        if (filled($this->getBlockByName($blockName, ['app', 'custom']))) {
            $this->error("Block '{$blockName}' already exists.");

            return;
        }

        $stubFileName = $blockStub->file->getPathName();

        if (!$blockStub->isNewFormat) {
            $this->error(
                "The block file '{$stubFileName}' format is the old one."
            );
            $this->error('Please upgrade it before using as template.');

            return;
        }

        $blockIdentifier = (new Block(
            $blockStub->file,
            $blockStub->type,
            $blockStub->source
        ))->makeName($blockName);

        $blockFile = resource_path(
            "views/admin/blocks/{$blockIdentifier}.blade.php"
        );

        $this->files->put(
            $blockFile,
            $this->makeBlock($stubFileName, $blockName, $iconName)
        );

        $this->info("Block {$blockName} was created.");

        $this->info("File: {$blockFile}");

        $this->info(
            "And it's ready to use: '{$blockIdentifier}'"
        );
    }

    public function makeBlock($stubFileName, $blockName, $iconName)
    {
        $stub = $this->files->get($stubFileName);

        $title = $this->makeBlockTitle($blockName);

        $stub = preg_replace(
            "/@a17-title\('(.*)'\)/",
            "@a17-title('{$title}')", $stub
        );

        $stub = preg_replace(
            "/@a17-icon\('(.*)'\)/",
            "@a17-icon('{$iconName}')", $stub
        );

        return $stub;
    }

    public function makeBlockTitle($string)
    {
        $string = 'quote_extended';

        $string = Str::kebab($string);

        $string = str_replace(['-', '_'], ' ', $string);

        return Str::title($string);
    }

    public function getBlockFile($name)
    {
        $blockFile = resource_path("views/admin/blocks/{$name}.blade.php");

        if ($this->files->exists($blockFile)) {
            $answer = $this->ask(
                "Local block file ({$blockFile}) exists. Replace it? (yes/no)"
            );

            if (Str::lower($answer) !== 'yes') {
                return;
            }
        }

        return $blockFile;
    }

    public function getBlockByName($block, $sources = [])
    {
        return $this->blockCollection->findByName($block, $sources);
    }

    public function getIconFile($icon)
    {
        $icon .= '.svg';

        return collect(
            $this->files->files(__DIR__ . '/../../frontend/icons')
        )->reduce(function ($keep, $file) use ($icon) {
            if ($keep) {
                return $keep;
            }

            return $file->getFilename() === $icon ? $file->getPathName() : null;
        }, null);
    }

    public function printArray($array)
    {
        $array = var_export($array, true);

        $array = "<?php return $array;";

        $array = str_replace(",\n", ",\n\n", $array);

        file_put_contents($file = 'test.file.php', $array);

        shell_exec(
            base_path("vendor/bin/php-cs-fixer fix {$file} --rules=@Symfony")
        );
    }
}
