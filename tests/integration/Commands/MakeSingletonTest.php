<?php

namespace A17\Twill\Tests\Integration\Commands;

use A17\Twill\Tests\Integration\TestCase;

class MakeSingletonTest extends TestCase
{
    public function testCanExecuteCommand()
    {
        $this->assertExitCodeIsGood(
            $this->artisan('twill:make:singleton', [
                'moduleName' => 'homepage',
                '--hasBlocks' => true,
                '--hasTranslation' => true,
                '--hasSlug' => true,
                '--hasMedias' => true,
                '--hasFiles' => true,
                '--hasRevisions' => true,
            ])->run()
        );

        $this->assertFileExists(
            twill_path('Http/Controllers/Twill/HomepageController.php')
        );

        $this->assertIsObject(
            $this->app->make(\App\Http\Controllers\Twill\HomepageController::class)
        );
    }
}
