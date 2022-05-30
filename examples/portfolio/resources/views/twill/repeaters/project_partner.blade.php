@twillRepeaterTitle('Project partner')
@twillRepeaterTitleField('title', ['hidePrefix' => true])
@twillRepeaterTrigger('Add partner')
@twillRepeaterSelectTrigger('Select partners')
@twillRepeaterGroup('app')

{{--
-- We use a disabled field to show some information about the partner.
--}}
<x-twill::input
    name="title"
    label="Title"
    :disabled="true"
    :translated="true"
/>

{{--
-- This is the role in the project, this field will not be stored in the partner model but will be stored
-- in the pivot table `partner_project`
-- In this example there's only one field, but a pivot field could contain multiple.
--}}
<x-twill::input
    name="role"
    label="role"
    note="The role in this project"
    :translated="true"
    :required="true"
/>
