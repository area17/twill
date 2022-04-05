@twillBlockTitle('Quote')
@twillBlockIcon('text')
@twillBlockGroup('twill')

@formField('input', [
    'name' => 'quote',
    'type' => 'textarea',
    'label' => 'Quote text',
    'maxlength' => 250,
    'rows' => 4
])

@formField('input', [
    'name' => 'author',
    'label' => 'Quote author',
])
