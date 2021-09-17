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

The idea of the `contentFields` section is to contain your most important fields and, if applicable, the block editor as the last field.

If you have other fields, like attributes, relationships, extra images, file attachments or repeaters, you'll want to add a `fieldsets` section after the `contentFields` section and use the `@formFieldset` directive to create new ones like in the following example:

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
    @formFieldset(['id' => 'attributes', 'title' => 'Attributes'])
        @formField('...', [...])
        ...
    @endformFieldset
@stop
```

The additional fieldsets array passed to the form layout will display a sticky navigation of your fieldset on scroll.
You can also rename the content section by passing a `contentFieldsetLabel` property to the layout, or disable it entirely using
`'disableContentFieldset' => true`.
