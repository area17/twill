@twillRepeaterTitle('Comment')
@twillRepeaterTitleField('title', ['hidePrefix' => false])
@twillRepeaterTrigger('Add comment')
@twillRepeaterGroup('app')

@formField('input', [
    'name' => 'title',
    'label' => 'Title'
])

@formField('input', [
    'name' => 'comment',
    'label' => 'The comment',
    'maxlength' => 100
])
