# Form Fields

## Using controller method

Since Twill 3.0 there is also a possibility to define your forms from the module controller,
for details about this approach see [Form builder documentation](../3_modules/7_form-builder.md).

This method will still continue to receive improvements as it does not yet support all the features
that the form views method has such as fieldset, sidebars and conditional fields.

## Using form views

Your module `form` view should look something like this (`resources/views/twill/moduleName/form.blade.php`):

```blade
@extends('twill::layouts.form')
@section('contentFields')
    <x-twill::input ... />
    ...
@stop
```

The idea of the `contentFields` section is to contain your most important fields and, if applicable, the block editor as
the last field.

If you have other fields, like attributes, relationships, extra images, file attachments or repeaters, you'll want to
add a `fieldsets` section after the `contentFields` section and use the `@formFieldset` directive to create new ones
like in the following example:

```blade
@extends('twill::layouts.form', [
    'additionalFieldsets' => [
        ['fieldset' => 'attributes', 'label' => 'Attributes'],
    ]
])

@section('contentFields')
    <x-twill::input ... />
    ...
@stop

@section('fieldsets')
   <x-twill::formFieldset id="attributes" title="Attributes" :open="false">
        <x-twill::input ... />
        ...
   </x-twill::formFieldset>
@stop
```

You can use the following arguments when defining a `formFieldset`

| Option | Description                                                                  | Type/values | Default value |
|:-------|:-----------------------------------------------------------------------------|:------------|:--------------|
| id     | The id of the fieldset, this should match the value in `additionalFieldsets` | string      |               |
| title  | The title of the fieldset                                                    | string      |               |
| open   | If the fieldset should be open by default                                    | boolean     | false         |

The additional fieldsets array passed to the form layout will display a sticky navigation of your fieldset on scroll.
You can also rename the content section by passing a `contentFieldsetLabel` property to the layout, or disable it
entirely using
`'disableContentFieldset' => true`.

## Sidebar

You can add content to the sidebar below the publication information by using the `sideFieldset` and `sideFieldsets`
sections.

This can be useful for small metadata, options or seo fields.

If you use the `sideFieldset` it will automatically be embedded into a collapsible fieldset called options

```blade
@section('sideFieldset')
    <x-twill::input
        name="description"
        label="Description"
        :translated="true"
        :maxLenght="100"
    />
@endsection
```

alternatively, or if you need more control, you can use the `sideFieldsets` section:

```blade
@section('sideFieldsets')
    <a17-fieldset title="SEO" id="seo">
        <x-twill::input
            name="description"
            label="Description"
            :translated="true"
            :maxLenght="100"
        />
        <x-twill::input
            name="meta"
            label="Meta"
            :translated="true"
            :maxLenght="100"
        />
    </a17-fieldset>
@endsection
```

Both combined produces the result as shown in the screenshot below:

<img style="width:50%; margin:32px auto;" src="/assets/screenshot-sidebar.png" />
