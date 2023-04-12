# Artisan Commands

Twill includes a few Artisan commands to facilitate the development process. They are all maintained under the `twill:` namespace.

Here is an overview:

* `php artisan twill:install {preset? : Optional, the preset to install} {--fromBuild}` - Detailed in the [installation section](../2_getting-started/2_installation.md) of the documentation, this command will generate and run core migrations for a starter Twill installation. Running this command after it has already been run can lead to errors and conflicts with your changes. After running database migrations, it will then automatically run the `twill:superadmin` command detailed below, in order to create a superadmin user, so you can log into your CMS.

* `php artisan twill:superadmin {email?} {password?}` - As noted above, this command will prompt you to create a new superadmin user, requesting a user email address and then a password. Run this command on its own if you need to quickly generate a new superadmin user.

* `php artisan twill:update {--fromBuild} {--migrate}` - Publish new updated Twill assets and optionally run database migrations

* `php artisan twill:lqip {--all=0}` - This command generates low-quality image placeholders (LQIP) of your media files as base64
  encoded strings that you can inline in your HTML response to avoid an extra image request. This is a strategy deployed
  in media management to improve initial page load times. The default behavior of this command is to generate LQIP for
  any media files that do not already have an LQIP alternative. Use the `--all` flag to generate new LQIP for all media
  files. To learn more about media management, check out the [media library section](../7_media-library) of the
  documentation.

* `php artisan twill:refresh-crops` - Refresh all crops for an existing image role. It may be crops defined in the Model or in config/twill.php.
```
php artisan twill:refresh-crops
  {modelName : The fully qualified model name (e.g. App\Models\Post, A17\Twill\Models\Block)}
  {roleName : The role name for which crops will be refreshed}
  {--dry : Print the operations that would be performed without modifying the database}` -
```

* `php artisan twill:make:module` - Generate a new module, see [CLI Generator](./../3_modules/2_cli-generator.md) 

```
php artisan twill:make:module {moduleName}
  {--B|hasBlocks}
  {--T|hasTranslation}
  {--S|hasSlug}
  {--M|hasMedias}
  {--F|hasFiles}
  {--P|hasPosition}
  {--R|hasRevisions}
  {--N|hasNesting}
  {--bladeForm}
  {--E|generatePreview}
  {--parentModel=}
  {--all}
```
  

* `php artisan twill:make:singleton` - Generate a new singleton, see [CLI Generator](./../3_modules/2_cli-generator.md)
```
php artisan twill:make:singleton {moduleName}
  {--B|hasBlocks}
  {--T|hasTranslation}
  {--S|hasSlug}
  {--M|hasMedias}
  {--F|hasFiles}
  {--R|hasRevisions}
  {--E|generatePreview}
  {--bladeForm}
  {--all}
```

* `php artisan twill:make:capsule` - Generate a new capsule, see [CLI Generator](./../3_modules/2_cli-generator.md)
```
php artisan twill:make:capsule {moduleName} 
  {--singleton} 
  {--packageDirectory=} 
  {--packageNamespace=}
  {--B|hasBlocks}
  {--T|hasTranslation}
  {--S|hasSlug}
  {--M|hasMedias}
  {--F|hasFiles}
  {--P|hasPosition}
  {--R|hasRevisions}
  {--N|hasNesting}
  {--E|generatePreview}
  {--bladeForm}
  {--all}
  {--force}
```

* `php artisan twill:list:icons {filter? : Filter icons by name}` - List available icons
* `php artisan twill:list:blocks` - List blocks
* `php artisan twill:make:package` - Generate a new Twill package
* `php artisan twill:make:componentBlock` - Generate a Twill block as a component
* `php artisan twill:build` - Build Twill assets with custom Vue components/blocks
