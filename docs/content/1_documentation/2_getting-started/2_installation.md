# Installation

You can install Twill on an existing Laravel application or a new one.

## Require the package

Twill package can be added to your application using Composer:

```bash
composer require area17/twill:"^3.0"
```

:::alert=type.warning:::
Before continuing, make sure you have updated your .env file with correct database credentials.
:::#alert:::

## Quickstart

If this is your first time using Twill or you just want to experiment with a Twill installation you can use the starter
kit to quickly get started.

:::alert:::
The [basic page builder guide](../../2_guides/1_page-builder-with-blade/1_index.md) is a step by step guide on how to
create exactly that what is in this example!
:::#alert:::

The starter kit setup is a basic page builder. It comes with:

- A page module
- A blade based frontend
- 2 example blocks to use in the block builder
- A navigation module
- A frontpage setting

This starter kit requires `kalnoy/nestedset` so install that first:

```bash
composer require kalnoy/nestedset
```

The install command is the same as above. Except that you pass the parameter `basic-page-builder` to install it.

```bash
php artisan twill:install basic-page-builder
```

## Manual new site

If you just want a clean slate to start with you can run `twill:install` without any additional arguments

```bash
php artisan twill:install
```

:::alert=type.danger:::
This command will migrate your database.

Make sure to setup your .env file with your database credentials and to run it where your database is accessible (ie.
inside Vagrant if you are using Laravel Homestead).
:::#alert:::

Twill's `install` command consists of:

- creating an `twill.php` routes files in your application's `routes` directory. This is where you will declare your own
  admin console routes.
- migrating your database with Twill's migrations.
- publishing Twill's configuration files to your application's `config` directory.
- publishing Twill's assets for the admin console UI.
- prompting you to create a superadmin user.

## Storage

If you have not yet done this following the Laravel installation guide, now would be a good time to run
`php artisan storage:link` to setup the storage folders mapping to the public directory.

## Admin path and domain

By default, Twill's admin console is available at `domain.test/admin`

### Using a subdomain

If you want to serve Twill from a subdomain you will have to set the admin app url as follows:

```bash
ADMIN_APP_URL=https://admin.domain.test
```

In development, make sure that the `admin` subdomain is available and pointing to your app's `public` directory.

If you are a Valet user, this is already done for you (any subdomain is linked to the same directory as the linked
domain).

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

If you just want to modify the admin path you can override just that:

```bash
ADMIN_APP_PATH=/cms
```

When running on 2 different subdomains (which is the default configuration as seen above), you want to share cookies
between both domains so that publishers can access drafts on the frontend. Use the `SESSION_DOMAIN` variable with your
domain, prefixed by a dot, like in the following example:

```bash
SESSION_DOMAIN=.domain.test
```

### Strict domain handeling

By default when using a path, Twill does not care about the domain you are on. But if you need this to be more strict
you can add `ADMIN_APP_STRICT=true` to your `.env` file.

This way, if `APP_URL` does not match your domain, it will not show the admin panel on the app path.

## Accessing the admin console

At this point, you should be able to login at `admin.domain.test`, `manage.domain.test` or `domain.test/admin` depending
on your environment configuration. You should be presented with a dashboard with an empty activities list, a link to
open Twill's media library and a dropdown to manage users, your own account and logout.

## Setting up the media library

From there, you might want to configure Twill's media library's storage provider and its rendering service.

By default Twill uses local storage and local image rendering using [Glide](https://glide.thephpleague.com/), if you
have more advanced image storage needs you can setup AWS as instructed below.

See the [media library's configuration documentation](/media-library/) for more information.

### AWS

Provide the following .env variables to get up and running to store uploads on `AWS S3` and to render
images via [Imgix](https://imgix.com)

```bash
S3_KEY=S3_KEY
S3_SECRET=S3_SECRET
S3_BUCKET=bucket-name

IMGIX_SOURCE_HOST=source.imgix.net
```

## A note about the frontend

On your frontend domain (`domain.test`), nothing changed, and that's ok! Twill does not make any assumptions regarding
how you might want to build your own applications. It is up to you to setup Laravel routes that queries content created
through Twill's admin console. You can decide to use server side rendering with Laravel's Blade templating and/or to
define API endpoints to build your frontend application using any client side solution (eg. Vue, React, Angular, ...).

On a clean Laravel install, you should still see Laravel's welcome screen. If you installed Twill on an existing Laravel
application, your setup should not be affected. Do not hesitate to reach out
on [GitHub](https://github.com/area17/twill/issues) if you have a specific use case or any trouble using Twill with your
existing application.
