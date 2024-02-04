# Capsules

Twill capsules are modules that are packaged with other functionality. They can be used to improve your projects code organization, they can be part of [packages](../14_packages/index.md) or your own code base.

You can even distribute capsules using GitHub.

## Installing existing capsules

You can find some capsules on the [Awesome Twill repository](https://github.com/area17/awesome-twill#capsules).
Alternatively, you can search by the [`twill-capsule`](https://github.com/topics/twill-capsule)
or [`twill-package`](https://github.com/topics/twill-package) tags on GitHub.

To install an existing capsule that is hosted on GitHub, for example the [Twill redirections capsule](https://github.com/area17/twill-capsule-redirections) we can run the following command:

```
php artisan twill:capsule:install
```

There are a few important arguments we can use:

- `--copy` makes a copy into your `app/Twill/Capsules` folder.
- `--require` uses composer require to include the capsule

The strategy to apply depends on your project. If you wish to make project specific changes to the capsule you should use the `--copy` method. Otherwise, if you are fine with how the capsule is, `--require` will do the trick.

So the full command to install the redirections capsule and copy it would be:

```
php artisan twill:capsule:install area17/twill-capsule-redirections --copy --branch=main
```

:::alert=type.info:::
We add `--main` because at the time of writing the is not yet a release made for this capsule. Usually this is not needed.
:::#alert:::

Once you have done that, we still need to add the capsule to our `config/twill-navigation.php` and enable it from our `config/twill.php` files.

The exact setup might vary from capsule to capsule, so best is to always check the capsule you are working with to see if they provide a readme.

In `config/twill.php`, tip: if you need to know the name, you can check the folder name in `app/Twill/Capsules`

```php
<?php

return [
    'capsules' => [
        'list' => [
            [
                'name' => 'Redirections',
                'enabled' => true,
            ],
        ],
    ],
    ...
];
```

And in `config/twill-navigation.php`

```php
return [
    'redirections' => [
        'title' => 'Redirections',
        'module' => true,
    ],
];
```

After this, make sure you run your migrations `php artisan migrate` and you should be good to go. Visit your Twill admin and the redirects module should be in the menu.

## Creating capsules

Before you dive into creating a capsule for your project, it is important to first decide whether you want to
redistribute it or if it is a project local way of organizing the code.

If you plan on redistributing, please head to the [packages](../14_packages/index.md) documentation as that is the new and preferred way to distribute Twill "plugins".

If you do not need to distribute, read along!

## Generating capsules

A capsule can be bootstrapped using the command `php artisan twill:make:capsule`, it accepts the same arguments as [the module cli generator](./2_cli-generator.md), if no arguments are passed it will ask during generation with the exception of `--singleton` which can be added if you want it to be a singleton module

```
php artisan twill:make:capsule Comment
```

Once the capsule is created, the cli output will tell you, similar to the above, what to add to your `config/twill-navigation.php` and `config/twill.php` files.

`config/twill.php`

```php
<?php

return [
    'capsules' => [
        'list' => [
            [
                'name' => 'Comments',
                'enabled' => true,
            ],
        ],
    ],
    ...
];
```

And in `config/twill-navigation.php`

```php
return [
    'comments' => [
        'title' => 'Comments',
        'module' => true,
    ],
];
```

Now that the capsule is generated, you can go and modify the contents of `app/Twill/Capsules/Comments` to the needs of your project!
