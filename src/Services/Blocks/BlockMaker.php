<?php

namespace A17\Twill\Services\Blocks;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Mockery\Exception;
use SplFileInfo;

class BlockMaker
{
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

    public function __construct(protected Filesystem $files, protected BlockCollection $blockCollection)
    {
    }

    public function getBlockCollection(): \A17\Twill\Services\Blocks\BlockCollection
    {
        return $this->blockCollection;
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
    public function make($blockName, $baseName, $iconName): bool
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
            $blockIdentifier
        );
    }

    /**
     * @param $baseName
     * @throws \Exception
     */
    protected function checkBlockStub($baseName): bool
    {
        if (blank($this->blockBase = $this->getBlockByName($baseName))) {
            $this->error(sprintf('Block \'%s\' doesn\'t exists.', $baseName));

            return false;
        }

        return true;
    }

    /**
     * @param $iconName
     */
    protected function checkIconFile($iconName): bool
    {
        if (blank($this->icon = $this->getIconFile($iconName))) {
            $this->error(sprintf('Icon \'%s\' doesn\'t exists.', $iconName));

            return false;
        }

        return true;
    }

    /**
     * @param $blockFile
     */
    protected function checkBlockFile($blockFile): bool
    {
        $this->info(sprintf('File: %s', $blockFile));

        if ($this->files->exists($blockFile)) {
            $this->error('A file with the same name already exists.');

            return false;
        }

        return true;
    }

    protected function checkBlockBaseFormat($stubFileName): bool
    {
        if (!$this->blockBase->isNewFormat) {
            $this->error(
                sprintf('The block file \'%s\' format is the old one.', $stubFileName)
            );

            $this->error('Please upgrade it before using as template.');

            return false;
        }

        return true;
    }

    protected function checkRepeaters($repeaters): bool
    {
        foreach ($repeaters as $repeater) {
            $this->info(sprintf('Repeater file: %s', $repeater['newRepeaterPath']));

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
    ): ?string {
        $stub = $stub ?? $this->files->get($stubFileName);

        $title = $this->makeBlockTitle($blockName);

        $stub = preg_replace(
            [
                "/@twillPropTitle\('(.*)'\)/",
                "/@twillBlockTitle\('(.*)'\)/",
                "/@twillRepeaterTitle\('(.*)'\)/",
            ],
            [
                sprintf('@twillPropTitle(\'%s\')', $title),
                sprintf('@twillBlockTitle(\'%s\')', $title),
                sprintf('@twillRepeaterTitle(\'%s\')', $title),
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
                sprintf('@twillPropIcon(\'%s\')', $iconName),
                sprintf('@twillBlockIcon(\'%s\')', $iconName),
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
     * @throws \Exception
     */
    protected function makeBlockIdentifier($blockName): string
    {
        return (new Block(
            $this->blockBase->file,
            $this->blockBase->type,
            $this->blockBase->source
        ))->makeName($blockName);
    }

    protected function makeBlockPath(string $blockIdentifier, string $type = 'block'): string
    {
        $destination = config(
            sprintf('twill.block_editor.directories.destination.%ss', $type)
        );

        if (!$this->files->exists($destination)) {
            if (
                !config('twill.block_editor.directories.destination.make_dir')
            ) {
                throw new Exception(
                    sprintf('Destination directory does not exists: %s', $destination)
                );
            }

            $this->files->makeDirectory($destination, 0755, true);
        }

        return sprintf('%s/%s.blade.php', $destination, $blockIdentifier);
    }

    /**
     * @param $string
     */
    public function makeBlockTitle($string): string
    {
        $string = Str::kebab($string);

        $string = str_replace(['-', '_'], ' ', $string);

        return Str::title($string);
    }

    /**
     * @param $block
     * @return mixed
     * @throws \Exception
     */
    public function getBlockByName($block, array $sources = [])
    {
        return $this->blockCollection->findByName($block, $sources);
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
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function generateRepeaters($baseName, $blockName, &$blockBase): ?\Illuminate\Support\Collection
    {
        preg_match_all(
            '#@formField(.*\'repeater\'.*\[.*=>.*\'(.*)\'].*)#',
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

                $oldRepeaterTag = $matches[0][0];
                $newRepeaterTag = str_replace(
                    sprintf('\'%s\'', $repeaterName),
                    sprintf('\'%s\'', $newRepeater['newRepeaterName']),
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
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Exception
     * @return array<string, mixed>
     */
    public function createRepeater(
        $repeaterName,
        $baseName,
        $blockName,
        $blockBase,
        $blockString
    ): array {
        $baseRepeater = $this->blockCollection->findByName($repeaterName);

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
                sprintf('\'%s\'', $repeaterName),
                sprintf('\'%s\'', $newRepeaterName),
                $blockString
            )),

            'newBlockStub' => str_replace(
                $blockString,
                $newBlockString,
                $blockBase
            ),
        ];
    }

    public function put($filePath, $contents): void
    {
        $directory = dirname($filePath);

        if (!$this->files->exists($directory)) {
            $this->files->makeDirectory($directory, 0755, true);
        }

        $this->files->put($filePath, $contents);
    }

    /**
     * @param $blockName
     */
    protected function saveAllFiles(
        $blockName,
        string $blockFile,
        \Illuminate\Support\Collection $repeaters,
        string $blockIdentifier
    ): bool {
        $this->put($blockFile, $this->blockBase);

        $this->info(sprintf('Block %s was created.', $blockName));

        foreach ($repeaters as $repeater) {
            $this->put(
                $repeater['newRepeaterPath'],
                $repeater['newRepeaterStub']
            );
        }

        $this->info(sprintf('Block is ready to use with the name \'%s\'', $blockIdentifier));

        return true;
    }

    public function setCommand(Command $command): static
    {
        $this->command = $command;

        return $this;
    }

    /**
     * @param $message
     */
    public function info($message): void
    {
        if ($this->command) {
            $this->command->displayInfo($message);
        }
    }

    /**
     * @param $message
     */
    public function error($message): void
    {
        if ($this->command) {
            $this->command->displayError($message);
        }
    }
}
