@twillBlockTitle('Demo')
@twillBlockIcon('text')
@twillBlockGroup('app')

<x-twill::input name="title" :translated="true" label="title field label"/>
<x-twill::input name="label" :translated="false" label="Label field" />
<x-twill::block-editor/>
