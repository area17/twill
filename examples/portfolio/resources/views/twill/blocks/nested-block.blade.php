@twillBlockTitle('Nested Block left right')
@twillBlockIcon('text')
@twillBlockGroup('app')

<x-twill::input
    name="title"
    label="Title"
    :translated="true"
/>

<x-twill::block-editor name="left"/>
<x-twill::block-editor name="right"/>
