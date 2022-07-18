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

We provide an automated upgrade path using `php artisan twill:upgrade`. This will take care of:
- Namespace changes in your project
- Moving the twill views to the new namespace
- Moving the twill routes to the new location

> ### Always make sure your git state is clean before attempting an update so you can roll back.

## Other changes

### withVideo on media defaults to false

Previously `withVideo` was true by default, if you relied on this you have to update these media fields to
`'withVideo' => true`.

### media/file library

The default for media and file libraries are now local and glide, if you relied on the default config for aws
then you now need to specify this in your `.env`.

### Block editor render children

The `renderBlocks` method now by default will NOT render the nested repeaters below the block. If you relied on this
you now need to update to `renderBlocks(true)`

### Crops

Model crops are now a global config, if you do not need model specific crops you can manage them globally from your
config.

### twillIncrementsMethod and twillIntegerMethod are removed

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

### Many methods now have typings to them

If you are overriding methods in your repository/controller or request classes. They may now
need typed arguments and return types.

This is an ongoing effort and will continue to occur as 3.x evolves (but not in bugfix releases).
