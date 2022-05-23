@twillBlockTitle('Carousel')
@twillBlockIcon('flex-grid')
@twillBlockGroup('twill')

<x-twill::select
    name="variation"
    label="Carousel variation"
    :options="[
        [ 'value' => 'fixed-width', 'label' => 'Fixed width' ],
        [ 'value' => 'variable-width', 'label' => 'Variable width' ]
    ]"
    default="fixed-width"
/>

<x-twill::repeater type="carousel-item"/>
