<?php

namespace A17\Twill\Tests\Integration\Commands;

use A17\Twill\Tests\Integration\TestCase;

class UpdateTest extends TestCase
{
    public function testCanExecuteUpdateCommand()
    {
        $this->assertExitCodeIsGood(
            $this->artisan('twill:update')
                ->expectsConfirmation('Do you want to run any pending database migrations now?', 'no')
                ->run()
        );
    }

    public function testCanExecuteUpdateWithMigrationOptionCommand()
    {
        $this->assertExitCodeIsGood(
            $this->artisan('twill:update --migrate')
                ->run()
        );
    }
}
