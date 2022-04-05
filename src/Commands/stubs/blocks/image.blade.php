@twillBlockCompiled('true')
@twillBlockComponent('a17-block-image')
@twillBlockTitle('Image')
@twillBlockIcon('image')
@twillBlockGroup('twill')

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
