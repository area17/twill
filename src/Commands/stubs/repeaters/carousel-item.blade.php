@twillBlockTitle('Carousel item')
@twillBlockTrigger('Add item')
@twillBlockGroup('twill')

@formField('input', [
    'name' => 'description',
    'label' => 'Description',
])

@formField('medias', [
    'name' => 'image',
    'label' => 'Image',
    'withVideoUrl' => false,
])
