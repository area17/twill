@title('Gallery')
@icon('editor')

@formField('select', [
    'name' => 'variation',
    'label' => 'Gallery variation',
    'options' => [
        [ 'value' => 'fixed-width', 'label' => 'Fixed width' ],
        [ 'value' => 'variable-width', 'label' => 'Variable width' ]
    ],
    'default' => 0
])

@formField('medias', [
    'name' => 'image',
    'label' => 'Images',
    'max' => 6,
    'withVideoUrl' => false,
    'extraMetadatas' => [
        [
            'name' => 'show_info',
            'label' => 'Show info',
            'type' => 'checkbox'
        ],
    ],
])
