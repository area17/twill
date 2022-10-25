# Revisions and Previewing

When using the `HasRevisions` trait, Twill's UI gives publishers the ability to preview their changes without saving, as well as to preview and compare old revisions.

If you are implementing your site using Laravel routing and Blade templating (ie. traditional server side rendering), you can follow Twill's convention of creating frontend views at `resources/views/site` and naming them according to their corresponding CRUD module name. When publishers try to preview their changes, Twill will render your frontend view within an iframe, passing the previewed record with it's unsaved changes to your view in the `$item` variable.

If you want to provide Twill with a custom frontend views path, use the `frontend` configuration array of your `config/twill.php` file:

```php
return [
    'frontend' => [
        'views_path' => 'site',
    ],
    ...
];
```

If you named your frontend view differently than the name of its corresponding module, you can use the $previewView class property of your module's controller:

```php
<?php
...

class ProjectController extends ModuleController
{
    protected $moduleName = 'projects';

    protected $previewView = 'custom-view-name';
    ...
}
```

If you want to provide the previewed view with extra variables or simply to rename the `$item` variable, you can implement the `previewData` function in your module's admin controller:

```php
<?php
...
protected function previewData($item)
{
    return [
        'project' => $item,
        'setting_name' => $settingRepository->byKey('setting_name')
    ];
}
```
