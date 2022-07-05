<?php

namespace A17\Twill\Tests\Unit\Components;

use A17\Twill\View\Components\Fields\BlockEditor;
use Illuminate\Support\Facades\View;

class BlockEditorFieldTest extends ComponentTestBase
{
    public string $component = BlockEditor::class;
    public string $field = \A17\Twill\Services\Forms\Fields\BlockEditor::class;
    public string $expectedView = 'twill::partials.form._block_editor';

    protected function setUp(): void
    {
        parent::setUp();

        // Required data for the block editor.
        View::share('form', ['form_fields' => ['blocks' => []]]);
    }
}
