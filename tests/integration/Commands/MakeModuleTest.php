<?php

namespace A17\Twill\Tests\Integration\Commands;

use A17\Twill\Tests\Integration\TestCase;

class MakeModuleTest extends TestCase
{
    public function testCanExecuteModuleCommand()
    {
        $this->assertExitCodeIsGood(
            $this->artisan('twill:make:module', [
                'moduleName' => 'Posts',
                '--hasBlocks' => true,
                '--hasTranslation' => true,
                '--hasSlug' => true,
                '--hasMedias' => true,
                '--hasFiles' => true,
                '--hasPosition' => true,
                '--hasRevisions' => true,
                '--hasNesting' => true,
            ])->run()
        );

        $this->assertFileExists(
            twill_path('Http/Controllers/Admin/PostController.php')
        );

        $this->assertIsObject(
            $this->app->make(\App\Http\Controllers\Admin\PostController::class)
        );
    }
}
