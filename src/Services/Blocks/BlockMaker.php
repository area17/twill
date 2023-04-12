<?php

namespace A17\Twill\Services\Blocks;

use A17\Twill\Facades\TwillBlocks;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Mockery\Exception;
use SplFileInfo;

class BlockMaker
{
    /**
     * @var Filesystem
     */
    protected $files;

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
     */
    public function __construct(
        Filesystem $files
    ) {
        $this->files = $files;
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
    public function make($blockName, $baseName, $iconName, bool $generateView = false)
    {
        $this->info('Creating block...');

        if (
            !$this->checkBlockStub($baseName) ||
            !$this->checkIconFile($iconName) ||
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
            $blockIdentifier,
            $generateView
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
            [
                "/@twillPropTitle\('(.*)'\)/",
                "/@twillBlockTitle\('(.*)'\)/",
                "/@twillRepeaterTitle\('(.*)'\)/",
            ],
            [
                "@twillPropTitle('{$title}')",
                "@twillBlockTitle('{$title}')",
                "@twillRepeaterTitle('{$title}')",
            ],
            $stub
        );

        $stub = preg_replace(
            [
                "/@twillPropGroup\('twill'\)/",
                "/@twillBlockGroup\('twill'\)/",
                "/@twillRepeaterGroup\('twill'\)/",
            ],
            [
                "@twillPropGroup('app')",
                "@twillBlockGroup('app')",
                "@twillRepeaterGroup('app')",
            ],
            $stub
        );

        $stub = preg_replace(
            [
                "/@twillPropIcon\('(.*)'\)/",
                "/@twillBlockIcon\('(.*)'\)/",
            ],
            [
                "@twillPropIcon('{$iconName}')",
                "@twillBlockIcon('{$iconName}')",
            ],
            $stub
        );

        $stub = preg_replace(
            [
                "/@twillPropCompiled\('(.*)'\)\n/",
                "/@twillBlockCompiled\('(.*)'\)\n/",
                "/@twillRepeaterCompiled\('(.*)'\)\n/",
            ],
            "",
            $stub
        );

        return preg_replace(
            [
                "/@twillPropComponent\('(.*)'\)\n/",
                "/@twillBlockComponent\('(.*)'\)\n/",
                "/@twillRepeaterComponent\('(.*)'\)\n/",
            ],
            "",
            $stub
        );
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
        return TwillBlocks::getBlockCollection()->findByName($block, $sources);
    }

    /**
     * @param $icon
     * @return mixed
     */
    public function getIconFile($icon, $addExtension = true)
    {
        if ($addExtension) {
            $icon .= '.svg';
        }

        return collect(
            config('twill.block_editor.directories.source.icons')
        )->reduce(function ($keep, $path) use ($icon) {
            if ($keep) {
                return $keep;
            }

            if (!$this->files->exists($path)) {
                return null;
            }

            return collect($this->files->files($path))->reduce(function ($keep, SplFileInfo $file) use ($icon) {
                if ($keep) {
                    return $keep;
                }

                return $file->getFilename() === $icon ? $file->getPathName() : null;
            }, null);
        }, null);
    }

    /**
     * @param $baseName
     * @param $blockName
     * @param $blockBase
     * @return \Illuminate\Support\Collection
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function generateRepeaters($baseName, $blockName, &$blockBase)
    {
        preg_match_all(
            '#<x-twill::repeater type="(.*)"\/>#',
            $blockBase,
            $matches
        );

        $repeaters = collect();

        if (count($matches) === 0) {
            return null;
        }

        foreach ($matches[1] as $index => $repeaterName) {
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

                $oldRepeaterTag = $matches[0][0];
                $newRepeaterTag = str_replace(
                    "'{$repeaterName}'",
                    "'{$newRepeater['newRepeaterName']}'",
                    $oldRepeaterTag
                );

                $blockBase = str_replace(
                    $oldRepeaterTag,
                    $newRepeaterTag,
                    $blockBase
                );

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
        $baseRepeater = TwillBlocks::getBlockCollection()->findByName($repeaterName);

        return [
            'baseRepeater' => $baseRepeater,

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
     * @param \Illuminate\Support\Collection $repeaters
     * @return bool
     */
    protected function saveAllFiles(
        $blockName,
        string $blockFile,
        $repeaters,
        string $blockIdentifier,
        bool $generateView = false
    ) {
        $this->put($blockFile, $this->blockBase);

        $this->info("Block {$blockName} was created.");

        if ($generateView) {
            $this->put(
                $path = str_replace('views/twill/blocks', 'views/site/blocks', $blockFile),
                'This is a basic preview. You can use dd($block) to view the data you have access to. <br />' .
                'This preview file is located at: ' . $path
            );
            $this->info("Block {$blockName} blank render view was created.");
        }

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
            $this->command->displayInfo($message);
        }
    }

    /**
     * @param $message
     */
    public function error($message)
    {
        if ($this->command) {
            $this->command->displayError($message);
        }
    }
}
