@twillBlockTitle('Article References')
@twillBlockIcon('text')
@twillBlockGroup('app')

<x-twill::wysiwyg
    name="text"
    label="Text"
    placeholder="Text"
    :toolbar-options="['bold', 'italic', 'link', 'clean']"
/>
