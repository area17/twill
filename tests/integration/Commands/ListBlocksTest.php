<?php

namespace A17\Twill\Tests\Integration\Commands;

use A17\Twill\Tests\Integration\Behaviors\CopyBlocks;
use A17\Twill\Tests\Integration\TestCase;

class ListBlocksTest extends TestCase
{
    use CopyBlocks;

    protected $allFiles = [];

    public function setup(): void
    {
        parent::setUp();

        $this->copyBlocks();
    }

    public function testCanListAllBlocks()
    {
        $this->execute();

        $this->assertFileExists(
            resource_path('views/twill/blocks/carousel.blade.php')
        );
    }

    public function testCanFilter()
    {
        $this->execute([
            'filter' => 'text',
        ]);

        // To avoid it being risky.
        $this->assertTrue(true);
    }

    public function testWorksFineWithZeroBlocks()
    {
        $this->execute([
            'filter' => 'there-are-no-blocks-here',
        ]);

        // To avoid it being risky.
        $this->assertTrue(true);
    }

    public function execute($parameters = [])
    {
        $pendingCommand = $this->artisan(
            'twill:list:blocks',
            $parameters
        );

        $this->assertExitCodeIsGood($pendingCommand->run());
    }
}
