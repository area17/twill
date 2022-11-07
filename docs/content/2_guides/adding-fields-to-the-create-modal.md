# Adding fields to the create modal

The default create modal has a field for the title and slug along with some publication settings:

![Default create modal](./assets/default-create-modal.png)

Often you might want to add some mandatory fields to the create modal of your modules.

Below are some examples:

## Basic customization

To customize the contents of the create modal we can add a `create.blade.php` next to the `form.blade.php` file.

There is 2 ways we can go about this, the first and usually preferred way is to extend from the default create file, this
will ensure that also with future updates you stay in line with the Twill defaults

```blade
@include('twill::partials.create')

<x-twill::input
    name="description"
    label="description"
    :translated="true"
    :maxlength="100"
/>
```

This will give you the following result:

![Customized create modal](./assets/customized-create-modal.png)

## Customize with more control

Alternatively you can go completely custom, and to reproduce the same outcome as above you can use the example below.

The benefit of this is that you have more control over the positioning and you could move the description field above
the permalink for example.

```blade
<x-twill::input
    :name="$titleFormKey ?? 'title'"
    :label="$titleFormKey === 'title' ? twillTrans('twill::lang.modal.title-field') : ucfirst($titleFormKey)"
    :translated="$translateTitle ?? false"
    :required="true"
    on-change="formatPermalink"
/>

<x-twill::input
    name="description"
    label="Description"
    :translated="true"
    :maxlength="100"
/>

@if ($permalink ?? true)
    <x-twill::input
        name="slug"
        :label="twillTrans('twill::lang.modal.permalink-field')"
        :translated="true"
        ref="permalink"
        :prefix="$permalinkPrefix ?? ''"
    />
@endif
```

## Providing data to the form

In some cases you might want to add a select list based on dynamic values coming from the back-end.

To do this you can utilize the `indexData` method on the module controller.

```php
class BlogController extends BaseModuleController
{
    ...
    protected function indexData($request)
    {
        return [
            'example_options' => [
                [
                    'value' => 'a',
                    'label' => 'This is A',
                ],
                [
                    'value' => 'b',
                    'label' => 'This is B',
                ],
                [
                    'value' => 'c',
                    'label' => 'This is C',
                ],
            ],
        ];
    }
}
```

Then in your modules `create.blade.php` file you can use:

```blade
@include('twill::partials.create')

<x-twill::select
    name="example_options_field"
    label="Example"
    :unpack="true"
    :options="$example_options"
/>
```

Result:

![Customized with options](./assets/customized-with-options.png)
