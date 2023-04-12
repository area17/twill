# Upgrading to 3.0 from 2.x

With Twill 3.x some files and classes are moved.

What changed:

- The `resources/views/admin` folder should be renamed `resources/views/twill`
- The `routes/admin.php` file should be renamed to `routes/twill.php`

Namespace changes:
```
app/Http/Controllers/Admin -> app/Http/Controllers/Twill
app/Http/Requests/Admin -> app/Http/Requests/Twill
```

Twill database table names are also updated to be prefixed by `twill_`.

We provide an automated upgrade path using the commands explained below. This will take care of:
- Namespace changes in your project
- Moving the twill views to the new namespace
- Moving the twill routes to the new location
- Fixing (most) compatibility issues
- Using the new TwillRoutes facade for ::module and ::singleton instead of a Route macro
- Updating your twill configuration to specify that you're using non prefixed database table names.

### Run the upgrade:

> ### Always make sure your git state is clean before attempting an upgrade so you can roll back.

```bash
php ./vendor/area17/twill/upgrade.php
```

### Other changes

#### Changes in admin app url/path

The admin url is now by default /admin instead of a subdomain. Please check the docs to change this to a [subdomain](https://twillcms.com/docs/getting-started/installation.html#content-using-a-subdomain) if
you were relying on that.

On top of that, this is now more "loose" and does not require the exact url. However, you can set it back to being
strict using:

`ADMIN_APP_STRICT=true`

#### WYSIWYG fields defaults to Tiptap

If you are relying on Quill.js specifics (like css classes), use `'type' => 'quill'` on your `wysiwyg` form fields.

#### withVideo on media defaults to false

Previously `withVideo` was true by default, if you relied on this you have to update these media fields to
`'withVideo' => true`.

#### SVG's are now no longer passing thorough glide

These are now rendered directly, you can change this by updating config `twill.glide.original_media_for_extensions` to an empty array `[]`

#### Media library

The default for media and file libraries are now local and glide, if you relied on the default config for S3 and Imgix
then you now need to [specify it](https://twillcms.com/docs/getting-started/installation.html#content-storage-on-s3) in your `.env`.

#### Block editor render children

The `renderBlocks` method now has the mapping as first argument.

The `renderBlocks` method now by default will NOT render the nested repeaters below the block. If you relied on this
you now need to update to `renderBlocks([], true)`

#### scopeForBucket replaced with getForBucket in featured

In Twill 2 scopeForBucket would return a collection of featured items. However, as the name illustrates, this
is not a scope.

In Twill 3 `scopeForBucket` is an actual scope and `getForBucket` is a helper to get the items directly.

#### Crops

Model crops are now a global config, if you do not need model specific crops you can manage them globally from your
config.

#### twillIncrementsMethod and twillIntegerMethod are removed

The default now is bigIncrements and bigInteger. If you relied on these functions for custom
logic you can add them to your own codebase. For reference the functions are below:

```php
if (!function_exists('twillIncrementsMethod')) {
    /**
     * @return string
     */
    function twillIncrementsMethod()
    {
        return config('twill.migrations_use_big_integers')
            ? 'bigIncrements'
            : 'increments';
    }
}

if (!function_exists('twillIntegerMethod')) {
    /**
     * @return string
     */
    function twillIntegerMethod()
    {
        return config('twill.migrations_use_big_integers')
            ? 'bigInteger'
            : 'integer';
    }
}
```

#### Many methods now have typings to them

If you are overriding methods in your repository/controller or request classes. They may now
need typed arguments and return types.

This is an ongoing effort and will continue to occur as 3.x evolves (but not in bugfix releases).
