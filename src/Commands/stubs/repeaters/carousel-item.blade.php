@a17-title('Carousel item')
@a17-trigger('Add item')
@a17-group('twill')

@formField('input', [
    'name' => 'description',
    'label' => 'Description',
    'translated' => true,
])

@formField('medias', [
    'name' => 'image',
    'label' => 'Image',
    'max' => 1,
    'withVideoUrl' => false,
])
