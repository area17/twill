<?php

namespace A17\CmsToolkit\Commands;

use Illuminate\Console\Command;

class Setup extends Command
{
    protected $signature = 'cms-toolkit:setup';

    protected $description = 'Setup the CMS Toolkit superadmin and publish assets/configs';

    public function fire()
    {
        $this->createSuperAdmin();
        $this->publishAssets();
        $this->publishConfigs();
    }

    private function createSuperAdmin()
    {
        $this->call('cms-toolkit:superadmin');
    }

    private function publishAssets()
    {
        $this->call('vendor:publish', [
            '--provider' => 'A17\CmsToolkit\CmsToolkitServiceProvider',
            '--tag' => 'assets',
        ]);
    }

    private function publishConfig()
    {
        $this->call('vendor:publish', [
            '--provider' => 'A17\CmsToolkit\CmsToolkitServiceProvider',
            '--tag' => 'config',
        ]);
    }
}
