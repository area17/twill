<?php

namespace A17\Twill\Tests\Browser;

use A17\Twill\Models\Media;
use A17\Twill\Services\Forms\Fields\BlockEditor;
use A17\Twill\Services\Forms\Form;
use A17\Twill\Tests\Integration\Behaviors\CreatesMedia;
use A17\Twill\Tests\Integration\Behaviors\FileTools;
use Laravel\Dusk\Browser;

class BlockEditorMediaTest extends BrowserTestCase
{
    use CreatesMedia;
    use FileTools;

    public function setUp(): void
    {
        parent::setUp();

        $block = <<<HTML
@twillBlockTitle('Image')
@twillBlockIcon('image')
@twillBlockGroup('app')

<x-twill::medias
    name="image"
    label="Image"
/>
HTML;

        // Copy the block to the installation.
        $this->putContentToFilePath(
            $block,
            resource_path('views/twill/blocks/image.blade.php')
        );

        $this->putContentToFilePath(
            '{{ $block->image("image", "desktop") }}',
            resource_path('views/site/blocks/image.blade.php')
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
            $browser->screenshot('1');
            $browser->createModuleEntryWithTitle('Command', 'Build');
            $browser->screenshot('2');

            $browser->clickLink('Open in editor');
            $browser->screenshot('3');
            // @note: Make sure that the cursor pointer is in the correct location.
            //
            // This is only required in a local environment when the browser is being displayed.
            $browser->drag('.editorSidebar__button', '.editorPreview__content');
            $browser->pause(4000);
            $browser->screenshot('4');
            $browser->waitForText('Attach image', 3);
            $browser->screenshot('5');
            $browser->press('Attach image');
            $browser->screenshot('6');

            $browser->waitFor('.mediagrid__button', 3);
            $browser->screenshot('7');
            $browser->click('.mediagrid__button');
            $browser->screenshot('8');
            $browser->waitForText('Insert image', 3);
            $browser->screenshot('9');
            $browser->press('Insert image');
            $browser->screenshot('10');

            $browser->waitFor('.editorPreview__frame iframe', 3);
            $browser->screenshot('11');

            $browser->withinFrame('.editorPreview__frame iframe', function (Browser $browser) {
                $browser->screenshot('12');
                $browser->waitForText('http://127.0.0.1:8001/img/uuid/area17.png', 3);
                $browser->screenshot('13');
                $browser->assertSee('http://127.0.0.1:8001/img/uuid/area17.png');
            });
        });
    }
}
