# Singleton

With the `twill:make:singleton` command you can generate a singleton module.

A singleton is similar to a module, except that with a singleton only one model can exist in the database.

A singleton can be used for a homepage or for example a contact page.

The functionalities are exactly the same as that of regular modules. But they are registered a bit differently.

After generating your singleton via the command mentioned above, a new entry will be added to `routes/twill.php`.

```php
<?php
use A17\Twill\Facades\TwillRoutes;

TwillRoutes::singleton('moduleName');
```

If you receive an error when visiting the singleton, you might have forgotten to run the seeder that is mentioned when generating the singleton. `php artisan db:seed ModuleNameSeeder`

If the singleton is not yet seeded when you visit the admin panel, by default Twill will automatically take care of the seeding.

You can disable this behavior by setting `twill.auto_seed_singletons` to false.
