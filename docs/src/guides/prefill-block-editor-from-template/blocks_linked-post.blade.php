@twillBlockTitle('Linked Article')
@twillBlockIcon('text')
@twillBlockGroup('app')

@formField('input', [
    'name' => 'title',
    'label' => 'Article Title',
])

@formField('input', [
    'name' => 'description',
    'label' => 'Article Link',
    'type' => 'textarea',
    'rows' => 4,
])

@formField('input', [
    'name' => 'url',
    'label' => 'Article URL',
])
