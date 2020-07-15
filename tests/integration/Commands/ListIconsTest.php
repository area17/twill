<?php

namespace A17\Twill\Tests\Integration\Commands;

use A17\Twill\Tests\Integration\TestCase;

class ListIconsTest extends TestCase
{
    protected $allFiles = [
        '{$tests}/../../frontend/icons/image.svg' => [
            '{$vendor}/area17/twill/frontend/icons/',
            '{$resources}/views/admin/icons/',
        ],

        '{$tests}/../../frontend/icons/video.svg' => [
            '{$vendor}/area17/twill/frontend/icons/',
            '{$resources}/views/admin/icons/',
        ],

        '{$tests}/../../frontend/icons/text.svg' => [
            '{$vendor}/area17/twill/frontend/icons/',
            '{$resources}/views/admin/icons/',
        ],
    ];

    public function setup(): void
    {
        parent::setUp();

        $this->copyFiles($this->allFiles);
    }

    public function testCanListIcons()
    {
        $this->assertExitCodeIsGood($this->artisan('twill:list:icons')->run());
    }
}
