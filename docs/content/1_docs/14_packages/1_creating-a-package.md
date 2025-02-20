# Creating a package

As mentioned in the introduction, a Twill package is at its core the same as a regular Laravel package.

If you are new to Laravel package development you can read more about it in the
[Laravel documentation](https://laravel.com/docs/10.x/packages).

## Generating your first Twill package

To make it a bit easier to get started we made a command you can use to kickstart your Twill package.

```bash
php artisan twill:make:package
```

After filling in the questions, a package with a `composer.json` and `TwillPackageServiceProvider` will be created.

The package generate command provides instructions on how to install it while working on it locally.

```bash
Your package has been generated!

You can now add it to your project's composer json repositories to work on it:

"repositories": [
    {
        "type": "path",
        "url": "./packages/twill-extension"
    },
],

Then you can require it: composer require area17/twill-extension

By default the package has no functionality, you can add a first capsule using (Replace YourModel with the model you want to use):

php artisan twill:make:capsule YourModel --singleton --packageDirectory=./packages/twill-extension --packageNamespace=TwillExtension\\YourModel

Enjoy!
```

And that's it, your first package is now created and enabled for your twill installation.

It is however, still empty so lets add a module as the instructions mention:

```bash
php artisan twill:make:capsule Project --packageDirectory=./packages/twill-extension --packageNamespace=TwillExtension\\Project
```

The command above will create a capsule, if you want to create a singleton capsule you could use `php artisan twill:make:capsule --singleton` instead.

Once this is done you already can go ahead and refresh your twill admin and the new capsule should appear in the menu!

If you get an error when visiting, do not forget to run your database migrations.

And that's it! You can now make Twill packages with capsules!


