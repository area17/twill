<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Services\Blocks\BlockMaker;
use Illuminate\Filesystem\Filesystem;

class IconsController extends Controller
{
    public function __construct(Filesystem $files, BlockMaker $blockMaker)
    {
        $this->files = $files;
        $this->blockMaker = $blockMaker;
    }

    public function show($file)
    {
        $file = $this->blockMaker->getIconFile($file, false);

        if (!$this->files->exists($file)) {
            abort(404);
        }

        return response()->stream(function () use ($file) {
            echo $this->files->get($file);
        });
    }
}
