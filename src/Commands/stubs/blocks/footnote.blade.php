@twillBlockTitle('Footnote')
@twillBlockIcon('text')
@twillBlockGroup('twill')

<x-twill::input
    name="anchor"
    label="Anchro"
/>

<x-twill::wysiwyg
    name="description"
    label="Text"
    :toolbar-options="[
        'bold',
        'italic',
        [ 'script' => 'super' ],
        [ 'script' => 'sub' ],
        'link',
        'clean'
    ]"
/>
