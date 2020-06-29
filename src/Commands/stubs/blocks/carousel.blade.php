@twillBlockTitle('Carousel')
@twillBlockIcon('flex-grid')
@twillBlockGroup('twill')

@formField('select', [
    'name' => 'variation',
    'label' => 'Carousel variation',
    'options' => [
        [ 'value' => 'fixed-width', 'label' => 'Fixed width' ],
        [ 'value' => 'variable-width', 'label' => 'Variable width' ]
    ],
    'default' => 0
])

@formField('repeater', ['type' => 'carousel-item'])
