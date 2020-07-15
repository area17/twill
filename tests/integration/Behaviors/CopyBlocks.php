<?php

namespace A17\Twill\Tests\Integration\Behaviors;

use Illuminate\Support\Str;

trait CopyBlocks
{
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
}
