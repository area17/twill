---
pageClass: twill-doc
title: Conditional fields
---

# Conditional fields


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



