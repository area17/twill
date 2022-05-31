<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Services\Blocks\BlockMaker;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\Finder\SplFileInfo;

class IconsController extends Controller
{
    protected Filesystem $files;

    protected BlockMaker $blockMaker;

    public function __construct(Filesystem $files, BlockMaker $blockMaker)
    {
        parent::__construct();

        $this->files = $files;
        $this->blockMaker = $blockMaker;
    }

    public function index()
    {
        $icons = collect(
            config('twill.block_editor.directories.source.icons')
        )->reduce(function (Collection $keep, $path) {
            if (! $this->files->exists($path)) {
                return $keep;
            }

            $files = collect($this->files->files($path))->map(function (
                SplFileInfo $file
            ) {
                if (in_array($file->getFilename(), config('twill.internal_icons'))) {
                    return null;
                }

                return [
                    'name' => Str::before($file->getFilename(), '.svg'),
                    'url' => route('twill.icons.show', [
                        'file' => $file->getFilename(),
                    ]),
                ];
            })->filter();

            return $keep->merge($files);
        }, collect());

        return view('twill::blocks.icons', ['icons' => $icons]);
    }

    public function show($file)
    {
        $file = $this->blockMaker->getIconFile($file, false);

        if (! $this->files->exists($file)) {
            abort(404);
        }

        return response()->stream(function () use ($file) {
            echo $this->files->get($file);
        }, 200, ['Content-Type' => 'image/svg+xml']);
    }
}
