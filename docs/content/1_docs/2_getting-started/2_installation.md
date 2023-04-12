# Installation

You can install Twill on an existing Laravel application or a new one.

## Quickstart

If this is your first time using Twill or you just want to experiment with a Twill installation you can use the starter kit to quickly get started.

:::alert:::
The [basic page builder guide](../../2_guides/1_page-builder-with-blade/1_index.md) is a step-by-step guide on how to create what is in this example!
:::#alert:::

The starter kit setup is a basic page builder. It comes with:

- A page module
- A blade based frontend
- 2 example blocks to use in the block builder
- A navigation module
- A frontpage setting

You can install it in a Laravel application using:

```bash
composer require area17/twill:"^3.0"
```

:::alert=type.warning::: 
This command will migrate your database.
Make sure to setup your .env file with your database credentials and to run it where your database is accessible (ie. using `sail` if you are using Laravel Sail or using Vagrant if you are using Laravel Homestead). 
:::#alert:::

```bash
php artisan twill:install basic-page-builder
```

See [`examples/basic-page-builder`](https://github.com/area17/twill/tree/3.x/examples/basic-page-builder) for detailed instructions on setting up the starter kit.

## Standard installation

Twill package can be added to your application using Composer:

```bash
composer require area17/twill:"^3.0"
```

:::alert=type.warning::: 
This command will migrate your database.
Make sure to setup your .env file with your database credentials and to run it where your database is accessible (ie. using `sail` if you are using Laravel Sail or using Vagrant if you are using Laravel Homestead).
:::#alert:::

```bash
php artisan twill:install
```

Twill's `install` command consists of:

- creating a `twill.php` routes files in your application's `routes` directory. This is where you will declare your own admin console routes.
- migrating your database with Twill's default migrations.
- publishing Twill's configuration file `twill.php` to your application's `config` directory.
- publishing Twill's assets for the admin console UI in your `public` directory.
- prompting you to create a superadmin user.

## Storage

If you have not yet done this following the Laravel installation guide, now would be a good time to run `php artisan storage:link` to set up the storage folders mapping to the public directory.

## Admin path and domain

By default, Twill's admin console is available at `domain.test/admin`

If you want to modify the `admin` path you can use the `ADMIN_APP_PATH` environment variable:

```bash
ADMIN_APP_PATH=/cms
```

### Using a subdomain

If you want to serve Twill from a subdomain you will have to set the admin app url as follows:

```bash
ADMIN_APP_URL=http://admin.domain.test
```

In development, make sure that the `admin` subdomain is available and pointing to your app's `public` directory.

If you are a Valet user, this is already done for you (any subdomain is linked to the same directory as the linked domain).

If you are a Homestead user, make sure to add the subdomain to your `/etc/hosts` file too:

```bash
# this is an example, use your own IP and domain
192.168.10.10 domain.test
192.168.10.10 admin.domain.test
```

When running Twill and your frontend on 2 different subdomains, you'll probably want to share cookies between both domains so that publishers can access drafts on the frontend (if you are allowing that in your integration). Use the `SESSION_DOMAIN` variable with your domain, prefixed by a dot, like in the following example:

```bash
SESSION_DOMAIN=.domain.test
```

Of course, you can specify a custom subdomain using the `ADMIN_APP_URL` variable. it doesn't have to be `admin`:

```bash
ADMIN_APP_URL=http://manage.domain.test
```

### Strict domain handling

By default, when using a path, Twill does not care about the domain you are on. But if you need this to be more strict
you can add `ADMIN_APP_STRICT=true` to your `.env` file.

This way, if `APP_URL` does not match your domain, it will not show the admin panel on the app path.

## Accessing the admin console

At this point, you should be able to login at `domain.test/admin`, `admin.domain.test` or `manage.domain.test` depending
on your environment configuration. You should be presented with a dashboard with an empty activities list, a link to
open Twill's media library and a dropdown to manage users, your own account and logout.

## Setting up the media library

From there, you might want to configure Twill's media library's storage provider and its rendering service.

By default, Twill uses your Laravel public storage directory to store uploads, and renders images dynamically using [Glide](https://glide.thephpleague.com/).

See the [media library's configuration documentation](./3_configuration.md#content-media-library) for more information.

### Storage on S3

Provide the following environment variables to get up and running to store uploads on S3 ([AWS](https://aws.amazon.com/s3/) or other S3 compliant services, like [Digital Ocean Spaces](https://www.digitalocean.com/products/spaces) and [Scaleway Object Storage](https://www.scaleway.com/en/object-storage/)):

```bash
S3_KEY=
S3_SECRET=
S3_BUCKET=
S3_REGION=
```

Twill's uploader is doing direct uploads from the browser to your S3 bucket, so you'll need to make sure the bucket CORS policy allows requests from your CMS domain. 

`S3_ROOT`, `S3_URL`, `S3_ENDPOINT` and `S3_USE_PATH_STYLE_ENDPOINT` variables are also available.

### Storage on Azure Blob storage

Provide the following environment variables to get up and running to store uploads on [Azure Blob Storage](https://azure.microsoft.com/en-us/products/storage/blobs):

```bash
AZURE_ACCOUNT_KEY=
AZURE_ACCOUNT_NAME=
```

Twill's uploader is doing direct uploads from the browser to your Blob Storage, so you'll need to make sure its CORS policy allows requests from your CMS domain.


`AZURE_CONTAINER`, `AZURE_ENDPOINT_SUFFIX`, `AZURE_UPLOADER_USE_HTTPS` variables are also available.

### Rendering images with an external service

Twill supports [Imgix](https://imgix.com) and [Twicpics](https://twicpics.com) to offload the rendering of responsive images. 

For example, with Imgix, assuming you configured a source on top of the S3 bucket storing your Twill uploads, you can use the following environment variables:

```bash
MEDIA_LIBRARY_IMAGE_SERVICE="A17\Twill\Services\MediaLibrary\Imgix"
IMGIX_SOURCE_HOST=source.imgix.net
```

`IMGIX_USE_SIGNED_URLS`, and `IMGIX_SIGN_KEY` variables are also available.

## A note about the frontend

On your frontend domain (`domain.test`), nothing changed, and that's ok! Twill does not make any assumptions regarding how you might want to build your own applications. It is up to you to setup Laravel routes that queries content created through Twill's admin console. You can decide to use server side rendering with Laravel's Blade templating and/or to define API endpoints to build your frontend application using any client side solution (eg. Vue, React, Angular, ...).

On a clean Laravel install, you should still see Laravel's welcome screen. If you installed Twill on an existing Laravel application, your setup should not be affected. Do not hesitate to reach out on [GitHub](https://github.com/area17/twill/discussions) or [Discord](https://discord.gg/cnWk7EFv8R) if you have a specific use case or any trouble using Twill with your existing application.
