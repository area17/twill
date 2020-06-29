@twillBlockTitle('Footnote')
@twillBlockIcon('text')
@twillBlockGroup('twill')

@formField('input', [
    'name' => 'anchor',
    'label' => 'Anchor',
])

@formField('wysiwyg', [
    'name' => 'description',
    'label' => 'Text',
    'toolbarOptions' => [
        'bold',
        'italic',
        [ 'script' => 'super' ],
        [ 'script' => 'sub' ],
        'link',
        'clean'
    ],
])
