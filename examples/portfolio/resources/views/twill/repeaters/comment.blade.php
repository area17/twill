@twillRepeaterTitle('Comment')
@twillRepeaterTitleField('title', ['hidePrefix' => false])
@twillRepeaterTrigger('Add comment')
@twillRepeaterGroup('app')

<x-twill::input
    name="title"
    label="Title"
/>

<x-twill::input
    name="comment"
    label="Comment"
    :maxlength="100"
/>
