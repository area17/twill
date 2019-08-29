### Installation

#### Composer
Twill is a package for Laravel applications, installable through Composer:

```bash
composer require area17/twill:"1.2.*"
```

Twill will automatically register its service provider if you are using Laravel `>=5.5`. 
If you are using Twill with Laravel `5.3` or `5.4`, add Twill's service provider in your application's `config/app.php` file:

```php{11}
<?php

'providers' => [
    ...
    Illuminate\Validation\ValidationServiceProvider::class,
    Illuminate\View\ViewServiceProvider::class,

    /*
     * Package Service Providers...
     */
    A17\Twill\TwillServiceProvider::class,
    ...

    /*
     * Application Service Providers...
     */
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    // App\Providers\BroadcastServiceProvider::class,
    App\Providers\EventServiceProvider::class,
    App\Providers\RouteServiceProvider::class,
    ...
];
```

#### Artisan

Run the `install` Artisan command: 

```bash
php artisan twill:install
```

:::danger
This command will migrate your database. 

Make sure to setup your .env file with your database credentials and to run it where your database is accessible (ie. inside Vagrant if you are using Laravel Homestead).
:::

Twill's `install` command consists of:
- creating an `admin.php` routes files in your application's `routes` directory. This is where you will declare your own admin console routes.
- publishing Twill's database migrations to your application's `database/migrations` directory.
- migrating your database with those new migrations.
- publishing Twill's configuration files to your application's `config` directory.
- publishing Twill's assets for the admin console UI.
- prompting you to create a superadmin user.


#### .env

By default, Twill's admin console is available at `admin.domain.test`. This is assuming that your .env `APP_URL` variable does not include a scheme (`http`/`https`):

```bash
APP_URL=domain.test
```

In development, make sure that the `admin` subdomain is available and pointing to your app's `public` directory. 

If you are a Valet user, this is already done for you (any subdomain is linked to the same directory as the linked domain). 

If you are a Homestead user, make sure to add the subdomain to your `/etc/hosts` file too:

```bash
# this is an example, use your own IP and domain
192.168.10.10 domain.test
192.168.10.10 admin.domain.test
```

Optionally, you can specify a custom admin console url using the `ADMIN_APP_URL` variable. For example:

```bash
ADMIN_APP_URL=manage.domain.test
```

As well as a path using the `ADMIN_APP_PATH` variable. For example, to have the admin console available on a subdirectory of your app (`domain.test/admin`):

```bash
APP_URL=domain.test
ADMIN_APP_URL=domain.test
ADMIN_APP_PATH=admin
```

When running on 2 different subdomains (which is the default configuration as seen above), you  want to share cookies between both domains so that publishers can access drafts on the frontend. Use the `SESSION_DOMAIN` variable with your domain, prefixed by a dot, like in the following example:

```bash
SESSION_DOMAIN=.domain.test
```

#### Accessing the admin console

At this point, you should be able to login at `admin.domain.test`, `manage.domain.test` or `domain.test/admin` depending on your environment configuration. You should be presented with a dashboard with an empty activities list, a link to open Twill's media library and a dropdown to manage users, your own account and logout.

#### Setting up the media library

From there, you might want to configure Twill's media library's storage provider and its rendering service. By default, Twill is configured to store uploads on `AWS S3` and to render images via [imgix](https://imgix.com). Provide the following .env variables to get up and running:

```bash
S3_KEY=S3_KEY
S3_SECRET=S3_SECRET
S3_BUCKET=bucket-name

IMGIX_SOURCE_HOST=source.imgix.net
```

If you are not ready to use those third party services yet, can't use them, or have very limited image rendering needs, Twill also provides a local storage driver as well as a locale image rendering service powered by [Glide](https://glide.thephpleague.com/). The following .env variables should get you up and running:

```bash
MEDIA_LIBRARY_ENDPOINT_TYPE=local
MEDIA_LIBRARY_IMAGE_SERVICE=A17\Twill\Services\MediaLibrary\Glide
```

See the [media library's configuration documentation](#media-library-2) for more information.

#### npm

Once you create custom blocks for your admin console, Twill's assets needs to be recompiled to include your generated Vue components.
In order to do that, add the following npm scripts to your project's `package.json`:

```json
"scripts": {
  "twill-build": "rm -f public/hot && npm run twill-copy-blocks && cd vendor/area17/twill && npm ci && npm run prod && cp -R public/* ${INIT_CWD}/public",
  "twill-copy-blocks": "npm run twill-clean-blocks && mkdir -p resources/assets/js/blocks/ && cp -R resources/assets/js/blocks/ vendor/area17/twill/frontend/js/components/blocks/customs/",
  "twill-clean-blocks": "rm -rf vendor/area17/twill/frontend/js/components/blocks/customs"
}
```

Build Twill's admin console UI assets using:

```bash
npm run twill-build
```

:::tip TIP
On Windows, depending on your configuration, you might want to add the `--script-shell bash` option when running `npm` commands.
Read more [here](https://github.com/area17/twill/issues/31#issuecomment-437557464).
:::

If you don't want to store Twill's compiled assets in Git, add the following to your project `.gitignore` :
```
public/assets/admin
public/mix-manifest.json
public/hot
```

If you are working on adding/modifying blocks for your application, or contributing to Twill itself, and would like to use Hot Module Reloading to propagate changes when recompiling blocks or modifying Twill, add and install the following dev dependencies to your project's `package.json`:

```json
"devDependencies": {
  "concurrently": "^3.5.1",
  "watch": "^1.0.2"
}
```

And the following npm scripts: 

```json
"scripts": {
  "twill-dev": "mkdir -p vendor/area17/twill/public && npm run twill-copy-blocks && concurrently \"cd vendor/area17/twill && npm ci && npm run hot\" \"npm run twill-watch\" && npm run twill-clean-blocks",
  "twill-watch": "concurrently \"watch 'npm run twill-hot' vendor/area17/twill/public --wait=2 --interval=0.1\" \"npm run twill-watch-blocks\"",
  "twill-hot": "cd vendor/area17/twill && cp -R public/* ${INIT_CWD}/public",
  "twill-watch-blocks": "watch 'npm run twill-copy-blocks' resources/assets/js/blocks --wait=2 --interval=0.1"
}
```

You can now start a Webpack HMR server on Twill's admin console UI assets using:

```bash
npm run twill-dev
```

This will refresh your browser tab or hot reload code when possible (keeping state when possible too) on any changes to your compiled blocks or Twill's frontend sources.


#### A note about the frontend

On your frontend domain (`domain.test`), nothing changed, and that's ok! Twill does not make any assumptions regarding how you might want to build your own applications. It is up to you to setup Laravel routes that queries content created through Twill's admin console. You can decide to use server side rendering with Laravel's Blade templating and/or to define API endpoints to build your frontend application using any client side solution (eg. Vue, React, Angular, ...).

On a clean Laravel install, you should still see Laravel's welcome screen. If you installed Twill on an existing Laravel application, your setup should not be affected. Do not hesitate to reach out on [Github](https://github.com/area17/twill/issues) if you have a specific use case or any trouble using Twill with your existing application.
