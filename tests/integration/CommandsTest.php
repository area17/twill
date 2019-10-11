<?php

namespace A17\Twill\Tests\Integration;

class CommandsTest extends TestCase
{
    public function testModuleCommand()
    {
        $this->artisan('twill:module', ['moduleName' => 'Posts']);

        $this->assertFileExists(twill_path('Http/Controllers/Admin/PostController.php'));

        $this->assertIsObject($this->app->make(\App\Http\Controllers\Admin\PostController::class));
    }
}
