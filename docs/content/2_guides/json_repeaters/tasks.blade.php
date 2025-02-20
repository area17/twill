@twillRepeaterTitle('Task')
@twillRepeaterTrigger('Add task')
@twillRepeaterGroup('app')

@formField('input', [
    'name' => 'name',
    'label' => 'Task name',
    'required' => true,
])

@formField('wysiwyg', [
    'name' => 'description',
    'label' => 'Description',
    'required' => true,
])

@formField('checkbox', [
    'name' => 'done',
    'label' => 'Done'
])
