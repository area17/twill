@a17-title('Carousel')
@a17-icon('flex-grid')

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
