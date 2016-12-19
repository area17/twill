<?php

namespace A17\CmsToolkit\Commands;

use File;
use Illuminate\Console\Command;

class UpdateCmsAssets extends Command
{
    protected $signature = 'cms-toolkit:update-assets';

    protected $description = 'Update CMS assets using latest release found on cms3.dev.area17.com';

    public function fire()
    {
        $assetsPath = public_path('assets/admin');

        if (!File::exists($assetsPath)) {
            File::makeDirectory($assetsPath, 0755, true);
        }

        $assets = ['a17cms.css', 'a17cms.js'];

        collect($assets)->each(function ($asset) use ($assetsPath) {
            File::put($assetsPath . '/' . $asset, file_get_contents('http://' . config('services.cms.credentials') . '@cms3.dev.area17.com/latest/' . $asset));
        });

    }
}
