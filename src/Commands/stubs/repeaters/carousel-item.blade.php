@twillRepeaterTitle('Carousel item')
@twillRepeaterTrigger('Add carousel item')
@twillRepeaterGroup('twill')

@formField('input', [
    'name' => 'description',
    'label' => 'Description',
])

@formField('medias', [
    'name' => 'image',
    'label' => 'Image',
    'withVideoUrl' => false,
])
