---
pageClass: twill-doc
---

# Form Fields

Your module `form` view should look something like this (`resources/views/admin/moduleName/form.blade.php`):

```php
@extends('twill::layouts.form')
@section('contentFields')
    @formField('...', [...])
    ...
@stop
```

The idea of the `contentFields` section is to contain your most important fields and, if applicable, the block editor as
the last field.

If you have other fields, like attributes, relationships, extra images, file attachments or repeaters, you'll want to
add a `fieldsets` section after the `contentFields` section and use the `@formFieldset` directive to create new ones
like in the following example:

```php
@extends('twill::layouts.form', [
    'additionalFieldsets' => [
        ['fieldset' => 'attributes', 'label' => 'Attributes'],
    ]
])

@section('contentFields')
    @formField('...', [...])
    ...
@stop

@section('fieldsets')
    @formFieldset(['id' => 'attributes', 'title' => 'Attributes', 'open' => false])
        @formField('...', [...])
        ...
    @endformFieldset
@stop
```

You can use the following arguments when defining a `formFieldset`

| Option      | Description                                                                  | Type/values    | Default value |
|:------------|:-----------------------------------------------------------------------------|:---------------|:--------------|
| id          | The id of the fieldset, this should match the value in `additionalFieldsets` | string         |               |
| title       | The title of the fieldset                                                    | string         |               |
| open        | If the fieldset should be open by default                                    | boolean        | false         |


The additional fieldsets array passed to the form layout will display a sticky navigation of your fieldset on scroll.
You can also rename the content section by passing a `contentFieldsetLabel` property to the layout, or disable it
entirely using
`'disableContentFieldset' => true`.

## Sidebar

You can add content to the sidebar below the publication information by using the `sideFieldset` and `sideFieldsets`
sections.

This can be useful for small metadata, options or seo fields.

If you use the `sideFieldset` it will automatically be embedded into a collapsible fieldset called options

```php
@section('sideFieldset')
    @formField('input', [
        'name' => 'description',
        'label' => 'Description',
        'translated' => true,
        'maxlength' => 100
    ])
@endsection
```

alternatively, or if you need more control, you can use the `sideFieldsets` section:

```php
@section('sideFieldsets')
    <a17-fieldset title="SEO" id="seo">
        @formField('input', [
            'name' => 'description',
            'label' => 'Description',
            'translated' => true,
            'maxlength' => 100
        ])
        @formField('input', [
            'name' => 'meta',
            'label' => 'Meta',
            'translated' => true,
            'maxlength' => 100
        ])
    </a17-fieldset>
@endsection
```

Both combined produces the result as shown in the screenshot below:

![screenshot](/docs/_media/screenshot-sidebar.png)
