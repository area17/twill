@a17-title('Image')
@a17-icon('image')

@formField('medias', [
    'name' => 'image',  // role
    'label' => 'Image',
    'withVideoUrl' => false,
    'translated' => false,
])

@formField('input', [
    'name' => 'author',
    'label' => 'Author',
    'translated' => false,
])

@formField('radios', [
    'name' => 'ratio',
    'label' => "Ratio",
    'options' => collect(['full' => 'Full', 'half' => 'Half']),
    'default' => 'full',
    'inline' => true
])
