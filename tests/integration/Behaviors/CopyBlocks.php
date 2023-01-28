<?php

namespace A17\Twill\Tests\Integration\Behaviors;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait CopyBlocks
{
    private $files;

    public function copyBlocks()
    {
        $allFiles = collect([
            'carousel.blade.php',
            'footnote.blade.php',
            'gallery.blade.php',
            'image.blade.php',
            'quote.blade.php',
        ])
            ->mapWithKeys(function ($block) {
                return [
                    "{\$tests}/../../src/Commands/stubs/blocks/{$block}" => collect(
                        config('twill.block_editor.directories.source.blocks')
                    )->map(function ($path) {
                        return $this->normalizeDir($path['path']);
                    }),
                ];
            })
            ->merge([
                "{\$tests}/../../src/Commands/stubs/repeaters/carousel-item.blade.php" => collect(
                    config('twill.block_editor.directories.source.repeaters')
                )->map(function ($path) {
                    return $this->normalizeDir($path['path']);
                }),
            ]);

        $this->copyFiles($allFiles);
    }

    protected function normalizeDir($directory)
    {
        if (!Str::endsWith($directory, DIRECTORY_SEPARATOR)) {
            $directory .= DIRECTORY_SEPARATOR;
        }

        return $directory;
    }

    /**
     * Copy all sources to destinations.
     */
    public function copyFiles(Collection $files): void
    {
        $this->files = app()->make(Filesystem::class);
        collect($files)->each(function ($destination, $source) {
            collect($destination)->each(function ($destination) use ($source) {
                $source = $this->makeFileName($source);

                $destination = $this->makeFileName($destination, $source);

                if (!$this->files->exists($directory = dirname($destination))) {
                    $this->files->makeDirectory($directory, 0755, true);
                }

                if ($this->files->exists($destination)) {
                    // @todo: Check why this removes the source file on my local environment.
                    // $this->files->delete($destination);
                }

                $this->files->copy($source, $destination);
            });
        });
    }
}
