---
pageClass: twill-doc
---

# Conditional Fields

You can conditionally display fields based on the values of other fields in your form. For example, if you wanted to display a video embed text field only if the type of article, a radio field, is "video" you'd do something like the following:

```php
@formField('radios', [
    'name' => 'type',
    'label' => 'Article type',
    'default' => 'long_form',
    'inline' => true,
    'options' => [
        [
            'value' => 'long_form',
            'label' => 'Long form article'
        ],
        [
            'value' => 'video',
            'label' => 'Video article'
        ]
    ]
])

@formConnectedFields([
    'fieldName' => 'type',
    'fieldValues' => 'video',
    'renderForBlocks' => true/false # (depending on regular form vs block form)
])
    @formField('input', [
        'name' => 'video_embed',
        'label' => 'Video embed'
    ])
@endformConnectedFields
```
Here's an example based on a checkbox field where the value is either true or false:

```php
@formField('checkbox', [
    'name' => 'vertical_article',
    'label' => 'Vertical Story'
])

@formConnectedFields([
    'fieldName' => 'vertical_article',
    'fieldValues' => true,
    'renderForBlocks' => true/false # (depending on regular form vs block form)
])
    @formField('medias', [
        'name' => 'vertical_image',
        'label' => 'Vertical Image',
    ])
@endformConnectedFields
```

Here's an example based on a browser field where the fields are displayed only when the browser field is not empty:

```php
@formField('browser', [
    'moduleName' => 'publications',
    'name' => 'related_publications',
    'label' => 'Related publications',
    'max' => 4,
])

@formConnectedFields([
    'fieldName' => 'publications',
    'isBrowser' => true,
    'keepAlive' => true,
])
    @formField('input', [
        'name' => 'related_publications_header',
        'label' => 'Related publications header',
    ])

    @formField('textarea', [
        'name' => 'related_publications_copy',
        'label' => 'Related publications copy',
    ])
@endformConnectedFields
```


| Option            | Description                                                                                      | Type              | Default value |
|:------------------|:-------------------------------------------------------------------------------------------------|:------------------|:--------------|
| fieldName         | Name of the connected field                                                                      | string            |               |
| fieldValues       | Value or values of the connected field that will reveal the fields in this component's slot      | string&vert;array |               |
| isEqual           | Controls how `fieldValues` are evaluated against the connected field                             | boolean           | true          |
| isBrowser         | Indicates that the connected field is a `browser` field                                          | boolean           | false         |
| matchEmptyBrowser | When set to true, the fields in this component's slot will be revealed when the browser is empty | boolean           | false         |
| keepAlive         | When set to true, the state of the hidden fields is preserved                                    | boolean           | false         |
| renderForBlocks   | When used inside a block, this needs to be set to true                                           | string            | false         |
