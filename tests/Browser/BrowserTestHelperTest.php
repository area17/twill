<?php

namespace A17\Twill\Tests\Browser;

use A17\Twill\Models\Block;
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

    public function testBlockEditorManual(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->superAdmin, 'twill_users');
            $browser->visitTwill();

            $browser->createModuleEntryWithTitle('Page', 'Example page');
            $browser->withinNewBlock('Text', function (Browser $browser, string $prefix) {
                $browser->type($prefix . '[title][en]', 'Hello world');
            });

            $browser->pressSaveAndCheckSaved('Save as draft');
        });

        $block = Page::latest()->first()->blocks()->first();

        $this->assertEquals('Hello world', $block->translatedInput('title'));
    }

//    public function testBlockEditorOverlay(): void
//    {
//        $this->browse(function (Browser $browser) {
//            $browser->loginAs($this->superAdmin, 'twill_users');
//            $browser->visitTwill();
//
//            $browser->createModuleEntryWithTitle('Page', 'Example page');
//
//            $browser->withinEditor(function (Browser $editor) {
//                $editor->dragBlock('Text', function (Browser $block, string $prefix) {
//                    $block->type($prefix . '[title][en]', 'Hello world');
//                });
//            });
//
//            $browser->pressSaveAndCheckSaved('Save as draft');
//        });
//
//        $block = Page::latest()->first()->blocks()->first();
//
//        $this->assertEquals('Hello world', $block->translatedInput('title'));
//    }

    public function testCanUploadAndAttachImage(): void
    {

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->superAdmin, 'twill_users');
            $browser->visitTwill();

            $browser->createModuleEntryWithTitle('Page', 'Example page');

            $browser->withinNewBlock('Image', function (Browser $browser, string $prefix) {
                $browser->attachImage('highlight', __DIR__ . '/../stubs/images/area17.png');
            });

            $browser->pressSaveAndCheckSaved('Save as draft');
        });

        /** @var Block $block */
        $block = Page::latest()->first()->blocks()->first();

        $this->assertEquals('area17.png', $block->medias()->first()->filename);
    }
}
