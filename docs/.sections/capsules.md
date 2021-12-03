## Capsules

Capsules contain all the same files as a module but are self-contained. This allows them to be installed from 3rd parties or shared between projects easily.

### CLI Generator

You can generate all the files needed to create a new capsule using Twill's Artisan generator:

```bash
php artisan twill:make:capsule capsuleName
```

The command accepts several options:
- `--hasBlocks (-B)`, to use the block editor on your module form
- `--hasTranslation (-T)`, to add content in multiple languages
- `--hasSlug (-S)`, to generate slugs based on one or multiple fields in your model
- `--hasMedias (-M)`, to attach images to your records
- `--hasFiles (-F)`, to attach files to your records
- `--hasPosition (-P)`, to allow manually reordering of records in the listing screen
- `--hasRevisions(-R)`, to allow comparing and restoring past revisions of records
- `--hasNesting(-N)`, to enable nested items in the module listing (see [Nested Module](#nested-module))

Capsules will be created in the `app/Twill/Capsules` directory

### Composer Installation

Capsules can be installed using Composer (`composer require vendor/capsule`). The only requirement is that the package declares its type to `twill-capsule`. An example package `composer.json` might look like this:

```
{
    "name": "vendor/package-name",
    "type": "twill-capsule",
    "require": {
        "area17/twill": "^2.5"
    },
    "autoload": {
        "psr-4": {
            "Vendor\\PackageName\\": "."
        }
    }
}
```

Capsules in the `app/Twill/Capsules` directory can co-exist with Composer installed Capsules so log as there is no naming conflicts

All twill-capsule packages published to Packagist are listed here: https://packagist.org/?type=twill-capsule
