# Upgrade to 2.x -> 3.x

With Twill 3.x there are some file movings and class changes.

Some of these actions we have provided [rector](https://github.com/rectorphp/rector) rules for. But still some actions
are required to make the upgrade go smooth.

## Rename views

The `resources/views/admin` folder should be renamed `resources/views/twill`

## Rename the routes file

The `routes/admin.php` file should be renamed to `routes/twill.php`

## Move repositories to twill subdirectory

Move all the repository files in `app/Repositories` to `app/Repositories/twill`

## Rename admin folders to twill

```
app/Http/Controllers/Admin -> app/Http/Controllers/Twill
app/Http/Requests/Admin -> app/Http/Requests/Twill
```

## Perform automated refactoring

As some files are moved it is important to first run `composer dump-autoload` to ensure composer autoloader is updated.

Then you can dry run the automated upgrade:

`vendor/bin/rector process app --dry-run --config=vendor/area17/twill/rector.php`

(The dry run will show you what will change, have a double check to make sure it wont introduce breaking changes for
your project)

Once everything is checked. you can remove the `--dry-run` argument to finalize the upgrade.

## Final touches

Finally run `php artisan migrate` and `php artisan twill:update` and you should be good to go!
