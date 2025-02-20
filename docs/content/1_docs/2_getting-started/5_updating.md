# Updating

To update between a minor version of Twill you can run the `php artisan twill:update` command. This will copy the javascript and css assets over to your public folder.

If you want to make it easy for you, you can add a post update action to your composer file to automate this every time you `composer update` ([docs](https://getcomposer.org/doc/articles/scripts.md#command-events)):

```
"post-autoload-dump": [
    ...
    "@php artisan twill:update --ansi"
],
```

:::alert=type.info:::
Updating Twill between minor version (ex 3.0 to 3.1) should rarely require manual intervention as we do our best to keep backwards compatability.
:::#alert:::

For major updates we try our best to provide a seamless upgrade experience, but we cannot always handle every case.

When upgrading for example 2.x to 3.x make sure you check the upgrade notes in the repository or in the [documentation page](6_upgrading.md).
