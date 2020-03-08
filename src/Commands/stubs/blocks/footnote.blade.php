@a17-title('Footnote')
@a17-icon('text')

@formField('input', [
    'name' => 'anchor',
    'label' => 'Anchor',
    'note' => ""
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
    'translated' => true
])
