@twillRepeaterTitle('Resource link')
@twillRepeaterTitleField('title', ['hidePrefix' => false])
@twillRepeaterTrigger('Add a resource link')
@twillRepeaterGroup('app')

@formField('input', [
    'name' => 'title',
    'label' => 'Title'
])

@formField('input', [
    'name' => 'url',
    'label' => 'Url',
    'type' => 'url'
])
