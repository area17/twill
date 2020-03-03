<?php

namespace A17\Twill\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use A17\Twill\Services\Blocks\Block;
use Illuminate\Filesystem\Filesystem;
use A17\Twill\Services\Blocks\Parser;

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
     * @param Parser $blockParser
     */
    public function __construct(Filesystem $files, Parser $blockParser)
    {
        parent::__construct();

        $this->files = $files;

        $this->blockParser = $blockParser;
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

        $blockIdentifier = (new Block())->makeName($blockName);

        $blockFile = resource_path(
            "views/admin/blocks/{$blockIdentifier}.blade.php"
        );

        $this->files->copy($stubFileName, $blockFile);

        $this->info("Block {$blockName} was created at {$blockFile}");

        $this->info(
            "You can use it already with the identifier '{$blockIdentifier}'"
        );
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
        return $this->blockParser->all()->findByName($block, $sources);
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
