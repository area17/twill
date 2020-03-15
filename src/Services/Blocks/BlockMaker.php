<?php

namespace A17\Twill\Services\Blocks;

use SplFileInfo;
use Mockery\Exception;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class BlockMaker
{
    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * @var \A17\Twill\Services\Blocks\BlockCollection
     */
    protected $blockCollection;

    /**
     * @var \Illuminate\Console\Command
     */
    protected $command;

    /**
     * @var \A17\Twill\Services\Blocks\Block
     */
    protected $blockBase;

    /**
     * @var string`
     */
    protected $icon;

    /**
     * @param Filesystem $files
     * @param \A17\Twill\Services\Blocks\BlockCollection $blockCollection
     */
    public function __construct(
        Filesystem $files,
        BlockCollection $blockCollection
    ) {
        $this->files = $files;

        $this->blockCollection = $blockCollection;
    }

    /**
     * Make a new block.
     *
     * @param $blockName
     * @param $baseName
     * @param $iconName
     * @return mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Exception
     */
    public function make($blockName, $baseName, $iconName)
    {
        $this->info('Creating block...');

        if (
            !$this->checkBlockStub($baseName) ||
            !$this->checkIconFile($iconName) ||
            !$this->checkBlockName($blockName) ||
            !$this->checkBlockBaseFormat(
                $stubFileName = $this->blockBase->file->getPathName()
            )
        ) {
            return false;
        }

        if (
            !$this->checkBlockFile(
                $blockFile = $this->makeBlockPath(
                    $blockIdentifier = $this->makeBlockIdentifier($blockName)
                )
            )
        ) {
            return false;
        }

        $this->blockBase = $this->makeBlock(
            $blockName,
            $iconName,
            $stubFileName
        );

        if (
            !$this->checkRepeaters(
                $repeaters = $this->generateRepeaters(
                    $baseName,
                    $blockIdentifier,
                    $this->blockBase
                )
            )
        ) {
            return false;
        }

        return $this->saveAllFiles(
            $blockName,
            $blockFile,
            $repeaters,
            $blockIdentifier
        );
    }

    /**
     * @param $baseName
     * @return bool
     * @throws \Exception
     */
    protected function checkBlockStub($baseName)
    {
        if (blank($this->blockBase = $this->getBlockByName($baseName))) {
            $this->error("Block '{$baseName}' doesn't exists.");

            return false;
        }

        return true;
    }

    /**
     * @param $iconName
     * @return bool
     */
    protected function checkIconFile($iconName)
    {
        if (blank($this->icon = $this->getIconFile($iconName))) {
            $this->error("Icon '{$iconName}' doesn't exists.");

            return false;
        }

        return true;
    }

    /**
     * @param $blockName
     * @return bool
     * @throws \Exception
     */
    protected function checkBlockName($blockName)
    {
        if (filled($this->getBlockByName($blockName, ['app', 'custom']))) {
            $this->error("Block '{$blockName}' already exists.");

            return false;
        }

        return true;
    }

    /**
     * @param $blockFile
     * @return bool
     */
    protected function checkBlockFile($blockFile)
    {
        $this->info("File: {$blockFile}");

        if ($this->files->exists($blockFile)) {
            $this->error('A file with the same name already exists.');

            return false;
        }

        return true;
    }

    protected function checkBlockBaseFormat($stubFileName)
    {
        if (!$this->blockBase->isNewFormat) {
            $this->error(
                "The block file '{$stubFileName}' format is the old one."
            );
            $this->error('Please upgrade it before using as template.');

            return false;
        }

        return true;
    }

    protected function checkRepeaters($repeaters)
    {
        foreach ($repeaters as $repeater) {
            $this->info("Repeater file: {$repeater['newRepeaterPath']}");

            if ($this->files->exists($repeater['newRepeaterPath'])) {
                $this->error(
                    'A repeater file with the same name already exists.'
                );

                return false;
            }
        }

        return true;
    }

    /**
     * @param $blockName
     * @param $iconName
     * @param $stubFileName
     * @param null|string $stub
     * @return string|string[]|null
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function makeBlock(
        $blockName,
        $iconName,
        $stubFileName = null,
        $stub = null
    ) {
        $stub = $stub ?? $this->files->get($stubFileName);

        $title = $this->makeBlockTitle($blockName);

        $stub = preg_replace(
            "/@a17-title\('(.*)'\)/",
            "@a17-title('{$title}')",
            $stub
        );

        $stub = preg_replace(
            "/@a17-group\('twill'\)/",
            "@a17-group('app')",
            $stub
        );

        $stub = preg_replace(
            "/@a17-icon\('(.*)'\)/",
            "@a17-icon('{$iconName}')",
            $stub
        );

        return $stub;
    }

    /**
     * @param $blockName
     * @return string
     * @throws \Exception
     */
    protected function makeBlockIdentifier($blockName)
    {
        return (new Block(
            $this->blockBase->file,
            $this->blockBase->type,
            $this->blockBase->source
        ))->makeName($blockName);
    }

    /**
     * @param string $blockIdentifier
     * @param string $type
     * @return string
     */
    protected function makeBlockPath(string $blockIdentifier, $type = 'block')
    {
        $destination = config(
            "twill.block_editor.directories.destination.{$type}s"
        );

        if (!$this->files->exists($destination)) {
            if (
                !config('twill.block_editor.directories.destination.make_dir')
            ) {
                throw new Exception(
                    "Destination directory does not exists: {$destination}"
                );
            }

            $this->files->makeDirectory($destination, 0755, true);
        }

        return "{$destination}/{$blockIdentifier}.blade.php";
    }

    /**
     * @param $string
     * @return string
     */
    public function makeBlockTitle($string)
    {
        $string = Str::kebab($string);

        $string = str_replace(['-', '_'], ' ', $string);

        return Str::title($string);
    }

    /**
     * @param $block
     * @param array $sources
     * @return mixed
     * @throws \Exception
     */
    public function getBlockByName($block, $sources = [])
    {
        return $this->blockCollection->findByName($block, $sources);
    }

    /**
     * @param $icon
     * @return mixed
     */
    public function getIconFile($icon)
    {
        $icon .= '.svg';

        return collect(
            $this->files->files(__DIR__ . '/../../../frontend/icons')
        )->reduce(function ($keep, SplFileInfo $file) use ($icon) {
            if ($keep) {
                return $keep;
            }

            return $file->getFilename() === $icon ? $file->getPathName() : null;
        }, null);
    }

    /**
     * @param $baseName
     * @param $blockName
     * @param $blockBase
     * @return \Illuminate\Support\Collection
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function generateRepeaters($baseName, $blockName, $blockBase)
    {
        preg_match_all(
            '/@formField(.*\'repeater\'.*\[.*=>.*\'(.*)\'].*)/',
            $blockBase,
            $matches
        );

        $repeaters = collect();

        if (count($matches) === 0) {
            return null;
        }

        foreach ($matches[2] as $index => $repeaterName) {
            if (Str::startsWith($repeaterName, $baseName)) {
                $newRepeater = $this->createRepeater(
                    $repeaterName,
                    $baseName,
                    $blockName,
                    $blockBase,
                    $matches[0][$index]
                );

                // Get the update version of the block stub, to be used on next repeaters
                $blockBase = $newRepeater['newBlockStub'];

                $repeaters->push($newRepeater);
            }
        }

        return $repeaters;
    }

    /**
     * @param $repeaterName
     * @param $baseName
     * @param $blockName
     * @param $blockBase
     * @param $blockString
     * @return array
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Exception
     */
    public function createRepeater(
        $repeaterName,
        $baseName,
        $blockName,
        $blockBase,
        $blockString
    ) {
        return [
            'baseRepeater' => ($baseRepeater = $this->blockCollection->findByName(
                $repeaterName
            )),

            'newRepeaterName' => ($newRepeaterName =
                $blockName . Str::after($repeaterName, $baseName)),

            'newRepeaterStub' => $this->makeBlock(
                $newRepeaterName,
                null,
                null,
                $baseRepeater->contents
            ),

            'newRepeaterTitle' => $this->makeBlockTitle($newRepeaterName),

            'newRepeaterPath' => $this->makeBlockPath(
                $newRepeaterName,
                Block::TYPE_REPEATER
            ),

            'newBlockString' => ($newBlockString = str_replace(
                "'{$repeaterName}'",
                "'{$newRepeaterName}'",
                $blockString
            )),

            'newBlockStub' => str_replace(
                $blockString,
                $newBlockString,
                $blockBase
            ),
        ];
    }

    public function put($filePath, $contents)
    {
        $directory = dirname($filePath);

        if (!$this->files->exists($directory)) {
            $this->files->makeDirectory($directory, 0755, true);
        }

        $this->files->put($filePath, $contents);
    }

    /**
     * @param $blockName
     * @param string $blockFile
     * @param \Illuminate\Support\Collection $repeaters
     * @param string $blockIdentifier
     * @return bool
     */
    protected function saveAllFiles(
        $blockName,
        string $blockFile,
        $repeaters,
        string $blockIdentifier
    ) {
        $this->put($blockFile, $this->blockBase);

        $this->info("Block {$blockName} was created.");

        foreach ($repeaters as $repeater) {
            $this->put(
                $repeater['newRepeaterPath'],
                $repeater['newRepeaterStub']
            );
        }

        $this->info("Block is ready to use with the name '{$blockIdentifier}'");

        return true;
    }

    /**
     * @param \Illuminate\Console\Command $command
     * @return BlockMaker
     */
    public function setCommand(Command $command)
    {
        $this->command = $command;

        return $this;
    }

    /**
     * @param $message
     */
    public function info($message)
    {
        if ($this->command) {
            $this->command->info($message);
        }
    }

    /**
     * @param $message
     */
    public function error($message)
    {
        if ($this->command) {
            $this->command->error($message);
        }
    }
}
