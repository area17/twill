---
pageClass: twill-doc
---

# CLI Generator

You can generate all the files needed in your application to create a new CRUD module using Twill's Artisan generator:

```bash
php artisan twill:make:module moduleName
```

The command accepts several options:
- `--hasBlocks (-B)`, to use the block editor on your module form
- `--hasTranslation (-T)`, to add content in multiple languages
- `--hasSlug (-S)`, to generate slugs based on one or multiple fields in your model
- `--hasMedias (-M)`, to attach images to your records
- `--hasFiles (-F)`, to attach files to your records
- `--hasPosition (-P)`, to allow manually reordering of records in the listing screen
- `--hasRevisions(-R)`, to allow comparing and restoring past revisions of records
- `--hasNesting(-N)`, to enable nested items in the module listing (see [Nested Module](/crud-modules/nested-modules.html))

The `twill:make:module` command will generate a migration file, a model, a repository, a controller, a form request object and a form view.

Add the route to your admin routes file(`routes/admin.php`).

```php
<?php

Route::module('moduleName');
```

Setup a new CMS navigation item in `config/twill-navigation.php`.

```php
return [
    ...
    'moduleName' => [
        'title'     => 'Module name',
        'module'    => true
    ]
    ...
]
```

With that in place, after migrating the database using `php artisan migrate`, you should be able to start creating content. By default, a module only have a title and a description, the ability to be published, and any other feature you added through the CLI generator.

If you provided the `hasBlocks` option, you will be able to use the `block_editor` form field in the form of that module.

If you provided the `hasTranslation` option, and have multiple languages specified in your `translatable.php` configuration file, the UI will react automatically and allow publishers to translate content and manage publication at the language level.

If you provided the `hasSlug` option, slugs will automatically be generated from the title field.

If you provided the `hasMedias` or `hasFiles` option, you will be able to respectively add several `medias` or `files` form fields to the form of that module.

If you provided the `hasPosition` option, publishers will be able to manually order  records from the module's listing screen (after enabling the `reorder` option in the module's controller `indexOptions` array).

If you provided the `hasRevisions` option, each form submission will create a new revision in your database so that publishers can compare and restore them in the CMS UI.

Depending on the depth of your module in your navigation, you'll need to wrap your route declaration in one or multiple nested route groups.

You can setup your index options and columns in the generated controller if needed.

## Singleton

With the `twill:make:singleton` command you can generate a singleton module.

A singleton is similar to a module, except that of a singleton only one model can exist in the database.

A singleton can be used for a homepage or for example a contact page.

The functionalities are exactly the same as that of regular modules. But they are registered a bit different.

After generating your singleton via the command mentioned above:

Add the route to your admin routes file(`routes/admin.php`).

```php
<?php

Route::twillSingleton('moduleName');
```

Setup a new CMS navigation item in `config/twill-navigation.php`.

```php
return [
    ...
    'moduleName' => [
        'title'     => 'Module name',
        'singleton' => true,
    ]
    ...
]
```

If you receive an error when visiting the module, you might have forgotton to run the seeder that is mentioned when
generating the singleton. `php artisan db:seed ModuleNameSeeder`

If you want you can also set the config variable `twill.auto_seed_singletons` to true, when the singleton admin page
is then visited and there is no content found it will be automatically seeded.
