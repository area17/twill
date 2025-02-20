@twillBlockTitle('Gallery')
@twillBlockIcon('editor')
@twillBlockGroup('twill')

<x-twill::select
    name="variation"
    label="Gallery variation"
    :options="[
        [ 'value' => 'fixed-width', 'label' => 'Fixed width' ],
        [ 'value' => 'variable-width', 'label' => 'Variable width' ]
    ]"
    default="fixed-width"
/>

<x-twill::medias
    name="image"
    label="Images"
    :max="20"
    :extra-metadatas="[
        [
            'name' => 'show_info',
            'label' => 'Show info',
            'type' => 'checkbox'
        ],
    ]"
/>
