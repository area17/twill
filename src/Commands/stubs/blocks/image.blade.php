@twillBlockCompiled('true')
@twillBlockComponent('a17-block-image')
@twillBlockTitle('Image')
@twillBlockIcon('image')
@twillBlockGroup('twill')

<x-twill::medias
    name="image"
    label="Image"
/>

<x-twill::input
    name="author"
    label="Author"
/>

<x-twill::radios
    name="ratio"
    label="Ratio"
    :options="collect(['full' => 'Full', 'half' => 'Half'])"
    default="full"
    :inline="true"
/>
