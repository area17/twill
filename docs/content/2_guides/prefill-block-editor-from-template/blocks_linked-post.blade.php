@twillBlockTitle('Linked Article')
@twillBlockIcon('text')
@twillBlockGroup('app')

<x-twill::input
    name="title"
    label="Article title"
/>

<x-twill::input
    name="description"
    label="Article link"
    type="textarea"
    :rows="4"
/>

<x-twill::input
    name="url"
    label="Article URL"
/>
