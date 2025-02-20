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
        $this->tweakApplication(function () {
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

            \A17\Twill\Tests\Integration\Anonymous\AnonymousModule::make('commands', app())
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

            $browser->press('Add content');
            $browser->waitForText('Image');
            $browser->click('.blocks__addButton');
            $browser->waitForText('Attach image', 3);

            $browser->clickLink('Open in editor');
            $browser->click('.editorPreview__dragger');
            $browser->waitForText('Attach image', 3);
            $browser->script('$(".media__field .button--ghost").click()');

            $browser->waitFor('.mediagrid__button', 3);
            $browser->click('.mediagrid__button');
            $browser->waitForText('Insert image', 3);
            $browser->press('Insert image');

            $browser->waitFor('.editorPreview__frame iframe', 3);

            $browser->withinFrame('.editorPreview__frame iframe', function (Browser $browser) {
                $browser->waitForText('http://127.0.0.1:8001/img/uuid/area17.png', 3);
                $browser->assertSee('http://127.0.0.1:8001/img/uuid/area17.png');
            });
        });
    }
}
