<?php

namespace A17\Twill\Http\Controllers\Admin;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;

class IconsController extends Controller
{
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    public function show($file)
    {
        $file = __DIR__ . "/../../../../frontend/icons/{$file}";

        if (!$this->files->exists($file)) {
            abort(404);
        }

        return response()->stream(function () use ($file) {
            echo $this->files->get($file);
        });
    }
}
