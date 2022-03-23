@twillBlockTitle('Gallery')
@twillBlockIcon('editor')
@twillBlockGroup('twill')

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
    'max' => 20,
    'extraMetadatas' => [
        [
            'name' => 'show_info',
            'label' => 'Show info',
            'type' => 'checkbox'
        ],
    ],
])
