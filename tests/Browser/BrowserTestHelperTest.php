<?php

namespace A17\Twill\Tests\Browser;

use App\Models\Page;
use Laravel\Dusk\Browser;

class BrowserTestHelperTest extends BrowserTestCase
{
    public ?string $example = 'basic-page-builder';

    public function testBlockEditor(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->superAdmin, 'twill_users');
            $browser->visitTwill();

            $browser->createModuleEntryWithTitle('Page', 'Example page');
            $browser->addBlockWithContent('Text', [
                'Title' => 'This is the title',
                'Text' => 'This is the wysiwyg'
            ]);

            $browser->pressSaveAndCheckSaved('Save as draft');
        });

        $block = Page::latest()->first()->blocks()->first();

        $this->assertEquals('This is the title', $block->translatedInput('title'));
        $this->assertStringContainsString('This is the wysiwyg', $block->translatedInput('text'));
    }
}
