<?php

namespace A17\Twill;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class PackageServiceProvider extends ServiceProvider
{
    protected function registerVueComponentsDirectory($path)
    {
        $this->publishes(
            [$path => resource_path(config('twill.vendor_components_resource_path'))],
            'components'
        );
    }

    protected function registerBlocksDirectory($path)
    {
        $blocks = Config::get('twill.block_editor.directories.source.blocks');

        $blocks[] = [
            'path' => $path,
            'source' => 'vendor',
        ];

        Config::set('twill.block_editor.directories.source.blocks', $blocks);
    }

    protected function registerRepeatersDirectory($path)
    {
        $repeaters = Config::get('twill.block_editor.directories.source.repeaters');

        $repeaters[] = [
            'path' => $path,
            'source' => 'vendor',
        ];

        Config::set('twill.block_editor.directories.source.repeaters', $repeaters);
    }
}
