# Conditional Fields

You can conditionally display fields based on the values of other fields in your form. For example, if you wanted to
display a video embed text field only if the type of article, a radio field, is "video" you'd do something like the
following:

```blade
<x-twill::radios
    name="type"
    label="Article type"
    default="long_form"
    :inline="true"
    :options="[
        [
            'value' => 'long_form',
            'label' => 'Long form article'
        ],
        [
            'value' => 'video',
            'label' => 'Video article'
        ]
    ]"
/>

<x-twill::formConnectedFields
    field-name="type"
    field-values="video"
    :render-for-blocks="true" {{-- Depends on the context --}}
>
    <x-twill::input
        name="video_embed"
        label="Video embed"
    />
</x-twill::formConnectedFields>
```

Here's an example based on a checkbox field where the value is either true or false:

```blade
<x-twill::checkbox
    name="vertical_article"
    label="Vertical story"
/>

<x-twill::formConnectedFields
    field-name="vertical_article"
    :field-values="true"
    :render-for-blocks="true" {{-- Depends on the context --}}
>
    <x-twill::medias
        name="vertical_image"
        label="Vertical Image"
    />
</x-twill::formConnectedFields>
```

Here's an example based on a checkboxes field where the values are stored in a json field:

```blade
@formField('checkboxes', [
    'name' => 'article_target',
    'label' => 'Target',
    'min' => 1,
    'max' => 3,
    'inline' => true,
    'options' => [
        [
            'value' => 'students',
            'label' => 'Students'
        ],
        [
            'value' => 'teachers',
            'label' => 'Teachers'
        ],
        [
            'value' => 'administration',
            'label' => 'Administration'
        ],
    ]
])

@formConnectedFields([
    'fieldName' => 'article_target',
    'fieldValues' => 'administration',
    'arrayContains' => false,
    'renderForBlocks' => true/false # (depending on regular form vs block form)
])
    @formField('files', [
        'name' => 'attachment',
        'label' => 'Attachment'
    ])
@endformConnectedFields
```

Here's an example based on a browser field where the fields are displayed only when the browser field is not empty:

```blade
<x-twill::browser
    module-name="publication"
    name="related_publications"
    label="Related publications"
    :max="4"
/>

<x-twill::formConnectedFields
    field-name="publication"
    :is-browser="true"
    :keep-alive="true"
>
    <x-twill::input
        name="related_publications_header"
        label="Related publications header"
    />
    <x-twill::input
        name="related_publications_copy"
        label="Related publications copy"
    />
</x-twill::formConnectedFields>
```

| Option            | Description                                                                                      | Type              | Default value |
|:------------------|:-------------------------------------------------------------------------------------------------|:------------------|:--------------|
| fieldName         | Name of the connected field                                                                      | string            |               |
| fieldValues       | Value or values of the connected field that will reveal the fields in this component's slot      | string&vert;array |               |
| isEqual           | Controls how `fieldValues` are evaluated against the connected field                             | boolean           | true          |
| isBrowser         | Indicates that the connected field is a `browser` field                                          | boolean           | false         |
| arrayContains     | Controls how `fieldValues` are evaluated when connected field is an array                        | boolean           | true          | 
| matchEmptyBrowser | When set to true, the fields in this component's slot will be revealed when the browser is empty | boolean           | false         |
| keepAlive         | When set to true, the state of the hidden fields is preserved                                    | boolean           | false         |
| renderForBlocks   | When used inside a block, this needs to be set to true                                           | string            | false         |
