### Artisan Commands
Twill includes a small number of Artisan commands to facilitate the development process. They are all maintained under the `twill:` namespace.

Here is an overview:

* `php artisan twill:install` - Detailed in the [installation section](#installation) of the documentation, this command will generate and run core migrations for a starter Twill installation. Running this command after it has already been run can lead to errors and conflicts. This is because it generates new migrations on the fly. The new migrations will not work correctly if previously generated migrations are already in the `database/migrations` folder, or if the databases linked to those migrations already exist in the database.<br/><br/>
After running the migrations, it will then automatically run the `twill:superadmin` command detailed below, in order to create a superadmin user so you can log into your starter CMS.

* `php artisan twill:superadmin` - As noted above, this command will prompt you to create a new superadmin user, requesting a user email address and then a password. Run this command on its own if you need to quickly generate a new superadmin user.

* `php artisan twill:module {moduleName}` - flags: `{--B|hasBlocks} {--T|hasTranslation} {--S|hasSlug} {--M|hasMedias} {--F|hasFiles} {--P|hasPosition} {--R|hasRevisions}` - generate a new module for your twill CMS. This command is extremely helpful in bootstrapping the files you will need to content manage new models. It is detailed extensively in the [CRUD Modules section](#cli-generator) of the documentation.

* `php artisan twill:blocks` - generate vue components from blade views. It is an essential step the process of enabling [block editing](#block-editor-3) on your site.

* `twill:lqip` - flags: ` {--all}` - generate low-quality image placeholders (LQIP) of your media files. This is a strategy deployed in media management to improve initial page load times. The default behavior of this command is to generate LQIP for any media files that do not already have an LQIP alternative. Use the `--all` flag to generate new LQIP for all media files. To learn more about media management, check out the [media library section](#media-library-3) of the documentation.
