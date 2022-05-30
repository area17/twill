@twillBlockTitle('Nested Block left right')
@twillBlockIcon('text')
@twillBlockGroup('app')

@formField('input', [
    'name' => 'title',
    'label' => 'Title',
    'translated' => true,
])

@formField('block_editor', ['name' => 'left'])
@formField('block_editor', ['name' => 'right'])
