---
pageClass: twill-doc
---

# Files

![screenshot](/docs/_media/files.png)

```php
<x-twill::files name="single_file" label="Single file"
                note="Add one file (per language)" />

<x-twill::files name="files" label="files" :max="4" />
```

::: details Old method
```php
@formField('files', [
    'name' => 'single_file',
    'label' => 'Single file',
    'note' => 'Add one file (per language)'
])

@formField('files', [
    'name' => 'files',
    'label' => 'Files',
    'max' => 4,
])
```
:::

| Option      | Description                               | Type/values    | Default value |
|:------------|:------------------------------------------|:---------------|:--------------|
| name        | Name of the field                         | string         |               |
| label       | Label of the field                        | string         |               |
| itemLabel   | Label used for the `Add` button           | string         |               |
| max         | Max number of attached items              | integer        | 1             |
| fieldNote   | Hint message displayed above the field    | string         |               |
| note        | Hint message displayed in the field       | string         |               |
| buttonOnTop | Displays the `Add` button above the files | true<br/>false | false         |


Similar to the media formField, to make the file field work, you have to include the `HasFiles` trait in your module's [Model](/crud-modules/models.html), and include `HandleFiles` trait in your module's [Repository](/crud-modules/repositories.html). At last, add the `filesParams` configuration array in your model.
```php
public $filesParams = ['file_role', ...]; // a list of file roles
```

Learn more at [Model](/crud-modules/models.html), [Repository](/crud-modules/repositories.html).

If you are using the file formField in a block, you have to define the `files` key in `config/twill.php`. Add it under `block_editor` key and at the same level as `crops` key:
```php
return [
    'block_editor' => [
        'crops' => [
            ...
        ],
        'files' => ['file_role1', 'file_role2', ...]
    ]
```

No migration is needed to save `files` form fields.
