<?php

namespace A17\Twill\Tests\Integration;

class MakeModuleTest extends TestCase
{
    public function testCanExecuteModuleCommand()
    {
        $this->artisan('twill:make:module', [
            'moduleName' => 'Posts',
            '--hasBlocks' => true,
            '--hasTranslation' => true,
            '--hasSlug' => true,
            '--hasMedias' => true,
            '--hasFiles' => true,
            '--hasPosition' => true,
            '--hasRevisions' => true,
        ]);

        $this->assertFileExists(
            twill_path('Http/Controllers/Admin/PostController.php')
        );

        $this->assertIsObject(
            $this->app->make(\App\Http\Controllers\Admin\PostController::class)
        );
    }
}
