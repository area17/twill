---
pageClass: twill-doc
---

# Artisan Commands

Twill includes a few Artisan commands to facilitate the development process. They are all maintained under the `twill:` namespace.

Here is an overview:

* `php artisan twill:install` - Detailed in the [installation section](/getting-started/installation.html) of the documentation, this command will generate and run core migrations for a starter Twill installation. Running this command after it has already been run can lead to errors and conflicts with your changes. After running database migrations, it will then automatically run the `twill:superadmin` command detailed below, in order to create a superadmin user so you can log into your CMS.

* `php artisan twill:superadmin` - As noted above, this command will prompt you to create a new superadmin user, requesting a user email address and then a password. Run this command on its own if you need to quickly generate a new superadmin user.

* `php artisan twill:module {moduleName}` - This command is extremely helpful in bootstrapping the files you will need to  manage new models. It is detailed extensively in the [CRUD Modules section](/crud-modules/cli-generator.html) of the documentation.

* `php artisan twill:lqip` - This command generates low-quality image placeholders (LQIP) of your media files as base64 encoded strings that you can inline in your HTML response to avoid an extra image request. This is a strategy deployed in media management to improve initial page load times. The default behavior of this command is to generate LQIP for any media files that do not already have an LQIP alternative. Use the `--all` flag to generate new LQIP for all media files. To learn more about media management, check out the [media library section](/media-library/) of the documentation.
