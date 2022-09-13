<?php

namespace A17\Twill\Tests\Browser;

use A17\Twill\Models\Media;
use A17\Twill\Services\Forms\Fields\BlockEditor;
use A17\Twill\Services\Forms\Form;
use A17\Twill\Tests\Integration\Behaviors\CreatesMedia;
use Laravel\Dusk\Browser;

class BlockEditorMediaTest extends BrowserTestCase
{
    use CreatesMedia;

    public function setUp(): void
    {
        parent::setUp();

        $this->ensureDirectoryExists(resource_path('views/site/blocks'));

        file_put_contents(
            resource_path('views/site/blocks/image.blade.php'),
            '{{ $block->image("image", "desktop") }}'
        );
    }

    public function testMediaCropsForNewBlocks(): void
    {
        $class = null;
        $this->tweakApplication(function () use (&$class) {
            $pathToStore = 'app/public/uploads/uuid';
            if (!Media::whereUuid('uuid/area17.png')->exists()) {
                if (!file_exists(storage_path($pathToStore))) {
                    mkdir(storage_path($pathToStore), 0777, true);
                }
                copy(__DIR__ . '/../stubs/images/area17.png', storage_path($pathToStore . '/area17.png'));

                Media::make()->forceFill([
                    'uuid' => 'uuid/area17.png',
                    'alt_text' => 'logo',
                    'width' => 398,
                    'height' => 258,
                    'filename' => 'area17.png',
                ])->save();
            }

            $class = \A17\Twill\Tests\Integration\Anonymous\AnonymousModule::make('commands', app())
                ->withFields([
                    'title' => [],
                ])
                ->withFormFields(
                    Form::make([
                        BlockEditor::make(),
                    ])
                )
                ->boot()
                ->getModelClassName();
        });

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->superAdmin, 'twill_users');
            $browser->visitTwill();
            $browser->createModuleEntryWithTitle('Command', 'Build');

            $browser->clickLink('Open in editor');
            // @note: Make sure that the cursor pointer is in the correct location.
            //
            // This is only required in a local environment when the browser is being displayed.
            $browser->drag('.editorSidebar__button', '.editorPreview__content');
            $browser->waitForText('Attach image');
            $browser->press('Attach image');

            $browser->waitFor('.mediagrid__button');
            $browser->click('.mediagrid__button');
            $browser->waitForText('Insert image');
            $browser->press('Insert image');

            $browser->waitFor('.editorPreview__frame iframe');

            $browser->withinFrame('.editorPreview__frame iframe', function (Browser $browser) {
                $browser->waitForText('http://127.0.0.1:8001/img/uuid/area17.png');
                $browser->assertSee('http://127.0.0.1:8001/img/uuid/area17.png');
            });
        });
    }
}
