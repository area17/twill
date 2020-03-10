# Changelog

All notable changes to `twill` will be documented in this file.

## 2.0.0 (2020-03-06)

We're really excited to release Twill 2.0 after a few months of focus to really set the project up for success. We've responded to the community pain points, supporting both Laravel 6 and 7, removing the need to build blocks and assets, improving documentation, introducing automated testing, and many more updates and bug fixes you can read more about below. 

We were also very positively surprised by the number and quality of external contributions. Twill now has [42](https://github.com/area17/twill/graphs/contributors) contributors, twice as much as our previous release, and community members are starting to provide excellent support to other developers from their experience working with it. Thanks a lot to everyone involved! Twill also surpassed [20k](https://packagist.org/packages/area17/twill/stats) installs recently!

We also want to note we understood the concerns shared by the community about our lack of releases in the past few months, and hope that this release will make you love working with Twill even more after patiently waiting for it. Our support for Laravel 6 took time to perfect, with dependencies going deprecated. Our changes to the frontend build or to the repositories traits needed to be challenged in different codebases. Stability is key for our users and it was important for us to take the time to make it right.

We could have tagged Laravel 6 support earlier though, that's entirely true, and that's something we want to address moving forward. We will now commit to releasing at least once every month. We might not want to be as quick as Laravel with a major release every 6 months, but we will be more actively releasing even if it is for a few minor fixes, that's for sure. With that said, we also want to say thank you to all the developers that tested our changes on the master branch during the past few months. It's been incredibly helpful to get feedback and contributions from the community.

We hope you enjoy this release, it is quite a big one. We're already excited about the next one!

**HOW TO UPDATE**

First, update your `composer.json` file by using: `"area17/twill": "^2.0"`.

Run `composer update` in your project and then, run Twill's own update command: `php artisan twill:update`. This will force update your published Twill assets. You can delete the old ones from your repository. 

If you're worrying about your custom blocks disappearing from the build, you should not! Blocks are now rendered at runtime, without you having to compile them from Blade to Vue components or wait for Twill to rebuild its assets anymore! Check out our changelog below to learn more.

Finally, you will need to migrate your database using `php artisan migrate` . Read more below on what might affect your existing codebase before doing so.

### Changed

 - [Semantic versioning](#semantic-versioning)
 - [Laravel versions support](#laravel-versions-support)
 - [Blocks and frontend build workflow](#blocks-and-frontend-build-workflow)
 - [Database migrations loading strategy](#database-migrations-loading-strategy)
 - [Database migrations changes](#database-migrations-changes)
 - [Translation models](#translation-models)

#### Semantic versioning

When releasing [Laravel 6](https://laravel-news.com/laravel-v6-announcement) at Laracon US last year, Taylor Otwell explained why v6 instead of v5.9, since it wasn't a "paradigm changing" release for the framework. That was because Laravel adopted [semantic versioning](https://semver.org/) (`major.minor.patch`). For simplicity, and because this is common practice for open source projects, we made that shift as well.

Starting with Twill 2.0.0, major releases are released only when breaking changes are necessary, while minor and patch releases may be released as often as every week. Minor and patch releases should never contain breaking changes.

When referencing Twill from your `composer.json` file, you should always use a version constraint such as `^2.0`, since major releases of Twill do include breaking changes.

> Until  recently, Laravel and Twill were following [romantic versioning](http://blog.legacyteam.info/2015/12/romver-romantic-versioning/) (`paradigm.major.minor`). This is why Twill 1.2.2 was not just about patches but new features and improvements as well. Because today's release includes breaking changes and Twill now follows semantic versionning, we have to tag it as `2.0.0`, even if it is not a paradigm shift at all.

#### Laravel versions support

Twill 2.0 supports Laravel 6 and 7, but does not support Laravel 5.4 and 5.5 anymore. 5.6, 5.7 and 5.8 are still supported.

We've removed all references to deprecated Laravel helpers from Twill, updated dependencies, and deleted some deprecated code from dropping support of 5.4 and 5.5.

We've also migrated from the deprecated [dimsav/laravel-translatable](https://github.com/dimsav/laravel-translatable) to [astronomic/laravel-translatable](https://github.com/Astrotomic/laravel-translatable).

We've removed the [Debug Bar](https://github.com/barryvdh/laravel-debugbar) and [Inspector](https://github.com/lsrur/inspector) debugging packages as Laravel now ships with [Ignition](https://flareapp.io/ignition) and we felt like developers should be able to pick the tools they prefer.

[`#389`](https://github.com/area17/twill/pull/389)/[`#456`](https://github.com/area17/twill/pull/456)/[`#561`](https://github.com/area17/twill/pull/561)

#### Blocks and frontend build workflow

It is not necessary to rebuild Twill's frontend when working with blocks anymore. Their templates are now dynamically rendered in Blade and loaded at runtime by Vue. Practically, it means you do not need to run `php artisan twill:blocks` and `npm run twill-build` after creating or updating a block. Just reload the page to see your changes after saving your Blade file!

This is possible because Twill's blocks Vue components are simple single file components that only have a template and a mixin registration. Blocks components are now dynamically registered by Vue using `x-template` scripts that are inlined by Blade.

In the process, we've also migrated from Laravel Mix to the latest version of Vue CLI, to have better control over our build. That also allowed us to fix an issue that had been annoying to quite a few users: conflicts with your own application's Laravel Mix configuration. Now, Twill's publishes it's manifest to its own directory with a custom name, and won't be a blocker to running both of your builds if necessary anymore.

If you are currently using custom Vue blocks (as in, you edited the `template`, `script` or `style` section of a generated block Vue file), you will still need to rebuild Twill assets as you used to, but we have a 2 new Artisan commands to help you and we recommend to use them instead of our previous versions' npm scripts:

 - `php artisan twill:build`, which will build Twill's assets with your custom blocks, located in the `twill.block_editor.custom_vue_blocks_resource_path` new configurable path (with defaults to `assets/js/blocks`, like in previous versions). 
 - `php artisan twill:dev`, which will start a local server that watches for changes in Twill's frontend directory. You need to set `'dev_mode' => true` in your `config/twill.php` file when using this command. This is especially helpful for Twill's contributors, but can also be useful if you use a lot of custom components in your application.

Both commands take a `--noInstall` option to avoid running `npm ci` before every build.

With that, it is now possible to define a block as being `compiled` in the `twill.block_editor.blocks` configuration array so that the imported Vue file is prefered at runtime over the inline, template-only, version, and so that you can use the new no-build workflow for all your regular blocks!

It is also possible to completely disable this feature by setting the `twill.block_editor.inline_blocks_templates` config flag to `false`.

If you do disable this feature, you could continue using previous versions's npm scripts, but we recommend you stop rebuilding Twill assets entirely unless you are using custom code in your generated Vue blocks. If you do keep using our npm scripts instead of our new Artisan commands, you will need to update `twill-build` from:
```
  "twill-build": "rm -f public/hot && npm run twill-copy-blocks && cd vendor/area17/twill && npm ci && npm run prod && cp -R public/* ${INIT_CWD}/public",
```
to:
```
  "twill-build": "npm run twill-copy-blocks && cd vendor/area17/twill && npm ci && npm run prod && cp -R dist/* ${INIT_CWD}/public",
```

On top of custom blocks, we've also made it possible to rebuild Twill with custom Vue components. This can be used to override Twill's own Vue components or create new form fields, for example. The new `twill.custom_components_resource_path` configuration can be used to provide a path under Laravel `resources` folder that will be used as a source of Vue components to include in your form js build when running `php artisan twill:build`.

We also namespaced our inline javascript variables to prevent any conflict in the global `window` moving forward. We know that `window.STORE` and `window.vm` were being used to hook into Twill's frontend application by some developers. [This commit](https://github.com/area17/twill/commit/9bc9c24925ea1bf857026f5f6a3db90ab099970f) tried to make sure to keep that working , but your mileage may vary if you are overriding Twill views. You should update to `window.TWILL.STORE` and `window.TWILL.vm` or even better, using `window.{{ config('twill.js_namespace') }}`  instead of directly using `window.TWILL` if you are in a Blade file and `process.env.VUE_APP_NAME` if you are in a Vue file.

Finally, to help custom workflows, maintainers and contributors, we made everything configurable:
- `manifest_file`, which defaults to `twill-manifest.json`
- `public_directory`, which defaults to `assets/admin`, like in previous versions, and can now be controlled through the `TWILL_ASSETS_DIR` environment variable
- `dev_mode`, which defaults to `false`
- `dev_mode_url`, which defaults to  [http://localhost:8080](http://localhost:8080/)  and can be controlled through the `TWILL_DEV_MODE_URL` environment variable.


[`d88ab7a0`](https://github.com/area17/twill/commit/d88ab7a0a05e50019d51e5d9398866826eaa1b21)/[`969e1260`](https://github.com/area17/twill/commit/969e1260a614c057b52260e8b93e7f1fa39793c6)/[`#510`](https://github.com/area17/twill/pull/510)/[`13a37fb5`](https://github.com/area17/twill/commit/13a37fb5d699f3ce44074ed6c2af3a925ae489e1)/[`c309a3a1`](https://github.com/area17/twill/commit/c309a3a1bb89f77a671da950c05943b152fa9ce5)/[`4a61875d`](https://github.com/area17/twill/commit/4a61875dc7eb3474bf7f1f6c17cc5858582b1bea)/[`9bc9c249`](https://github.com/area17/twill/commit/9bc9c24925ea1bf857026f5f6a3db90ab099970f)/[`dc0c5043`](https://github.com/area17/twill/commit/dc0c504348579a3732a069e7c97e68abbe2feeee)/[`43f4f6e1`](https://github.com/area17/twill/commit/43f4f6e1c2fb86a9491cd4191f841b463b4a8a9c)/[`b86e8d2d`](https://github.com/area17/twill/commit/b86e8d2db690e43ce26d81c04126c75d7adcc98f)/[`f80278c0`](https://github.com/area17/twill/commit/f80278c0703d2942a9150d2b192abda29e11c5d8)/[`482af7fd`](https://github.com/area17/twill/commit/482af7fd1cc30d342ab4ddcb18d0ae98062000c8)/[`6676c8e0`](https://github.com/area17/twill/commit/6676c8e0c0f480c131798e0030e9a6fc858a9601)/[`3ca864bc`](https://github.com/area17/twill/commit/3ca864bca95f3809fdecfa4342699ec510cb39a0)/[`ea3d7a99`](https://github.com/area17/twill/commit/ea3d7a99885ed1b1742ab8ca47bfa02e7558eb9b)/[`1cfd81e2`](https://github.com/area17/twill/commit/1cfd81e2a21be1045608f075440c99f3228cf7e9)/[`c183b914`](https://github.com/area17/twill/commit/c183b9144fe4c69c7ffd30e05a6516e13159693e)/[`20f2e022`](https://github.com/area17/twill/commit/20f2e02231208139b5a57534eafe4c46de24e250)/[`0a0692bf`](https://github.com/area17/twill/commit/0a0692bfac99238e3a0bd2b72139f94637c4586c)/[`fb0236f2`](https://github.com/area17/twill/commit/fb0236f2543657640c7a7c3d59c87cc591769155)/[`4cfd4f61`](https://github.com/area17/twill/commit/4cfd4f611ff992801d13ef5bd003bfeb700f95d8)/[`ed4de74f`](https://github.com/area17/twill/commit/ed4de74feb20fd60267b1b06f1ec58cbd2a74da8)/[`e37b4cd1`](https://github.com/area17/twill/commit/e37b4cd1c65ab778324fe932bb86dbecd096f049)/[`228105a3`](https://github.com/area17/twill/commit/228105a31a953ecf2f5ca56f60d82db3df675b2d)/[`fbad6585`](https://github.com/area17/twill/commit/fbad6585edbcc3a9d73331d06b18123e53a7e253)/[`d4f04f6b`](https://github.com/area17/twill/commit/d4f04f6b50d12415ea7ea2ddf572e866ba17834f)/[`2ac51b3c`](https://github.com/area17/twill/commit/2ac51b3c219680947561234801d59c9784f96a6f)/[`5f49f67c`](https://github.com/area17/twill/commit/5f49f67c061af397ea5224efa4c5907b696aae33)/[`471f654f`](https://github.com/area17/twill/commit/471f654faa95c27281a7a2df1fef0caa310d4308)/[`160743b4`](https://github.com/area17/twill/commit/160743b4ab06cf46ce20c4c1992069ddd2082060)/[`b92c9d95`](https://github.com/area17/twill/commit/b92c9d95e007570e17f5ea6e136657e292c528f5)/[`0f326d59`](https://github.com/area17/twill/commit/0f326d594452cc22b8ef3bb21e2fa594449d2379)/[`82b35ac2`](https://github.com/area17/twill/commit/82b35ac2df0b2a9ae39f5fbf0187d9eea65226c4)/[`cedbea45`](https://github.com/area17/twill/commit/cedbea45c06f8afd4da1989704802b5084e67258)/[`9a754806`](https://github.com/area17/twill/commit/9a75480625412c3b499a7e557bdfded742f6fe8a)/[`45ff20c1`](https://github.com/area17/twill/commit/45ff20c1223b3facb8916014ab1f732d9348a188)

#### Database migrations loading strategy

As recommended by [Laravel's documentation](https://laravel.com/docs/7.x/packages#migrations), we've decided to load Twill's database migrations without publishing them. This will allow more flexibility in the future and it avoids polluting the host application migrations folder. 

A boolean config key has been introduced to control this new behavior: `twill.load_default_migrations`. It defaults to `true` starting with Twill 2.0. 

Even if you are migrating from a Twill 1.x application, you should not have to worry about running those new migrations as they have been modified to always check for existence (or inexistence) of tables and columns before doing anything. If you want to maintain migrations yourself, feel free to disable this option and use Twill's `migrations` folder as a source of truth to update yours.

We've also prepared for all tables to be prefixed by `twill_` in the next major release and exposed new config keys to control their names so you can already start using prefixed tables with Twill 2.0.

[`372#issuecomment-537965676`](https://github.com/area17/twill/pull/372#issuecomment-537965676)/[`7fced605`](https://github.com/area17/twill/commit/7fced6057183e624b0acdf0805f4513b1d9b9623)/[`ee489635`](https://github.com/area17/twill/commit/ee4896353054f7da4a54a964d78acba025cc8400)

#### Database migrations changes
Like Laravel, Twill now uses big integers in migrations helpers. This is a breaking change with backwards compatibility provided through the `twill.migrations_use_big_integers` configuration key.

 [`b3fd5819`](https://github.com/area17/twill/commit/b3fd5819b0cde430c7cda3543d4242927892de05)


#### Translation models
Twill now automatically takes care of your translations models `fillable` by reusing your `translatedAttributes` array as long as you define a `$baseModuleModel`, which now happens automatically when generating modules from the CLI. This is not a breaking change but we think you should update to this new approach to avoid duplicating your columns list in 2 files.

[`#414`](https://github.com/area17/twill/pull/414)/[`c4e3c3fa`](https://github.com/area17/twill/commit/c4e3c3fa75daa0c37641f9f4bc1395870f9d7908)/[`957702ac`](https://github.com/area17/twill/commit/957702ac0ecd84e541293d7790e96d6200358412)/[`7a783deb`](https://github.com/area17/twill/commit/7a783debf8e2e8af60ce2f3938673c66f8e8d33d)/[`cc958d4d`](https://github.com/area17/twill/commit/cc958d4d4282d76fa81f0d725bbc07c03c85724d)/[`c8151ede`](https://github.com/area17/twill/commit/c8151eded958c475d1a040bdbc1011fe10d05dc0)

### Added

 - [Smarter CLI](#smarter-cli)
 - [Automated testing](#automated-testing)
 - [OAuth login](#oauth-login)
 - [JSON fields groups](#json-fields-groups)
 - [Azure uploads](#azure-uploads)
 - [CMS i18n](#i18n)
 - [Duplicate records](#duplicate-records)
 - [Destroy records in the trash](#destroy-records-in-the-trash)
 - [Automated browsers and repeaters](#automated-browsers-and-repeaters)
 - [Subdomain routing](#subdomain-routing)
 - [Tiptap WYSIWYG](#tiptap-wysiwyg)
 - [And more...](#and-more)


#### Smarter CLI
![cli](https://twill.io/docs/changelogs_media/cli.png)

Twill's `module` command now offers available options through a series of questions and then generates model and migration files content dynamically depending on provided options, removing previous versions comments, providing a greatly improved developer experience. 

You can use the new  `--all` option to enable all traits without any prompt. When providing no option, the prompt defaults to yes for all options. When providing one or multiple options, the prompt defaults to no for all other options. 

It is possible to use artisan's `--no-interaction` option to skip the prompt.

[`c73154f2`](https://github.com/area17/twill/commit/c73154f2bd91d83d8fa735bb8467e3aea9004805)/[`a9de8155`](https://github.com/area17/twill/commit/a9de8155d0319e47483bdaa36f9866aed029ddc4)

#### Automated testing
![enter image description here](https://twill.io/docs/changelogs_media/tests.png)

TravisCI is now testing Twill on all currently supported and future PHP versions. Almost 60% of all the PHP code is now covered by a PHPUnit test.
Syntax errors are being checked by PHP-CS-Fixer and [Scrutinizer CI](https://scrutinizer-ci.com/g/area17/twill/) is now analyzing Twill's codebase at every single commit. Prettier is now configured to maintain Twill's PSR-2 style. PHPStan also helped us fixing a few issues.

[`38e224ea`](https://github.com/area17/twill/commit/38e224ea9e6d8c93d5304e5162acec0f56772598)/[`e1e67949`](https://github.com/area17/twill/commit/e1e67949ae576e229d1350df665fd1f9c549c932)/[`213b2c9b`](https://github.com/area17/twill/commit/213b2c9bcd2d3e96af794d8a8f2ad8c5a70bc804)/[`#516`](https://github.com/area17/twill/pull/516)/[`5af10938`](https://github.com/area17/twill/commit/5af109386e9ebad0341aead0d53ad39a34d96507)/[`#517`](https://github.com/area17/twill/pull/517)/[`3a3605b6`](https://github.com/area17/twill/commit/3a3605b6ad9358f3ec997739ea4d896e2ce5aea2)/[`#533`](https://github.com/area17/twill/pull/533)[`94362dc5`](https://github.com/area17/twill/commit/94362dc593c8a681e1e9d6a1eb96ae6f65573ee5)/[`3d468aa4`](https://github.com/area17/twill/commit/3d468aa431a734686b818311f160b0761d4d8e2e)/[`c287dc18`](https://github.com/area17/twill/commit/c287dc18bc3abd3970651ab3174c1741af40e30a)/[`18b069f3`](https://github.com/area17/twill/commit/18b069f3f95cf935d3dd42a831feea3f760010ed)/[`d0cab04c`](https://github.com/area17/twill/commit/d0cab04c25f6bd78551d1c8652f0807ef1b71d31)/[`f6d2e720`](https://github.com/area17/twill/commit/f6d2e72034857f6744266219bbd806efa27f0a3c)/[`c13a267a`](https://github.com/area17/twill/commit/c13a267a98738260efae87f578c34f4c8b661c62)/[`9f30604d`](https://github.com/area17/twill/commit/9f30604d888b8d446ecf98fbe2322b7e235c986e)/[`35749445`](https://github.com/area17/twill/commit/35749445e1199b84aa5971809878b5831ccd1c8e)/[`85c2730c`](https://github.com/area17/twill/commit/85c2730c83404ec489a8a3a3d9561b939ee02ef5)/[`915053eb`](https://github.com/area17/twill/commit/915053eba1b8e39c799b06623d797cf8030384b3)/[`0c753c4f`](https://github.com/area17/twill/commit/0c753c4ffd9ea965afa9271e7177d743965adacc)/[`3c7e9a82`](https://github.com/area17/twill/commit/3c7e9a82466451821fcf3d939abedfd0ca036914)/[`3956339f`](https://github.com/area17/twill/commit/3956339fb51b0fc03c32557945e224a4c0464261)/[`6416e41b`](https://github.com/area17/twill/commit/6416e41bd42a49a40215cb009e34568d52decfcc)/[`7cb0ac44`](https://github.com/area17/twill/commit/7cb0ac44dd4268c309caa5033705232b895fda01)/[`21436f5c`](https://github.com/area17/twill/commit/21436f5c2d1deddc152f60eb2f51ba62590f370d)/[`8f8316bb`](https://github.com/area17/twill/commit/8f8316bbc0e6706283a97e0c8cbd9a0d548d4ed7)/[`ff2b37e6`](https://github.com/area17/twill/commit/ff2b37e6fa48f2fc6b0cb00c084600c14ee0f0ef)/[`466e2cc0`](https://github.com/area17/twill/commit/466e2cc0381c67b77ac65a1a48d4109cf126cfb0)/[`c7ad1b91`](https://github.com/area17/twill/commit/c7ad1b91e55aceda532d226d4648919bef7d0843)/[`2dd11547`](https://github.com/area17/twill/commit/2dd11547b4ba0a862c79d8f4e005bf2d4274ca30)/[`5d2f7eb0`](https://github.com/area17/twill/commit/5d2f7eb02f1a2ae2ef70198ef07614911eeae68a)/[`c6f8a1ec`](https://github.com/area17/twill/commit/c6f8a1ec2b47578bbca697ae72856edf5f4663a0)/[`2df4e94d`](https://github.com/area17/twill/commit/2df4e94d9a149652ab9d51e148f997139ad70491)/[`6c59927a`](https://github.com/area17/twill/commit/6c59927a16400da8233bdcb101ff8d52b4ecbe21)/[`6a92f46b`](https://github.com/area17/twill/commit/6a92f46b833eb9ac81365e91ce351cefbf1857c0)/[`5cf42a51`](https://github.com/area17/twill/commit/5cf42a510bbda53f48ddfe4dc87aa0564c474c03)/[`5082beb6`](https://github.com/area17/twill/commit/5082beb6b18a90229ab9b68edb89139fd766e575)/[`4c5dda9e`](https://github.com/area17/twill/commit/4c5dda9e9beeb6615afa49212a1178900d7798ad)/[`57c770db`](https://github.com/area17/twill/commit/57c770db407f0eaf5033aa8e824583398c816e22)/[`9599a055`](https://github.com/area17/twill/commit/9599a055d1520354eaf12d72b9f48e896ed3cfdf)/[`fc801000`](https://github.com/area17/twill/commit/fc801000ed985cf2291b30c1e282ca60de5d9787)/[`10669f1f`](https://github.com/area17/twill/commit/10669f1f7a2d9ce7b309ddfea5540acd12f671e6)/[`cc3d5637`](https://github.com/area17/twill/commit/cc3d56378772970b3a6e82933f6cd2d835c81c00)/[`0b75750e`](https://github.com/area17/twill/commit/0b75750eb6697b788ba33470c28617591809b12a)/[`723f8c08`](https://github.com/area17/twill/commit/723f8c089f1825b6c26dbdbfe4eba53db6378bbe)/[`ceb6b39d`](https://github.com/area17/twill/commit/ceb6b39d0e73a2cab75bfc0a1490f1e82772158a)/[`90898bbc`](https://github.com/area17/twill/commit/90898bbcb7e6064e385eeb0548c3e11c095ab6f1)/[`c86b9700`](https://github.com/area17/twill/commit/c86b97000d8f9ac17a2d7a2b3651974a7582f110)/[`2819959f`](https://github.com/area17/twill/commit/2819959f383f288f0af4edd6f5ba32ff824369dc)/[`094a9fab`](https://github.com/area17/twill/commit/094a9fabc72ec4559808a9935b417253f8b0256d)/[`2c2ea3ec`](https://github.com/area17/twill/commit/2c2ea3ecf4fda3118b4859e7ccb1064cdb13ec8d)/[`9639281b`](https://github.com/area17/twill/commit/9639281bbb63ed760a203d99259d6484958fcf60)/[`727416e4`](https://github.com/area17/twill/commit/727416e487fc8e0358971dd524ee5b2602724155)/[`cb0cb0f7`](https://github.com/area17/twill/commit/cb0cb0f7281872571533d40c9eb918bfad51a0b7)/[`6892e5a4`](https://github.com/area17/twill/commit/6892e5a46273b0e2092b434d18f12ca4cb5dd02b)/[`acddf89d`](https://github.com/area17/twill/commit/acddf89da092b52a50f69fb443087107d696a250)/[`dfab755b`](https://github.com/area17/twill/commit/dfab755b345d184e012dc6f4f43b3ce2f3c5b0a5)/[`2df955e9`](https://github.com/area17/twill/commit/2df955e9a77e235cb6dcbd24c6c0ebe9d76dc4d6)/[`c4b8b599`](https://github.com/area17/twill/commit/c4b8b5994f3ef03883e645415bcaf56ed7ffd22b)/[`18405838`](https://github.com/area17/twill/commit/1840583860af5b312a8ae911937f17cc6d4ae1a5)/[`ca929ecd`](https://github.com/area17/twill/commit/ca929ecdcae22dfc79f59a9ce7a1a4cf40356544)/[`7fa3a133`](https://github.com/area17/twill/commit/7fa3a133d6c5895e5393a48dccb5a5f7c3e30873)/[`708c8ced`](https://github.com/area17/twill/commit/708c8ced1b19a635b1bdb1af02c537383624bb3e)/[`8bd24c28`](https://github.com/area17/twill/commit/8bd24c2895da74b32d7dcc291a795be64be7e521)/[`28de0782`](https://github.com/area17/twill/commit/28de0782da821b64aa54a2155cf45183e96c21c5)/[`42f48b08`](https://github.com/area17/twill/commit/42f48b089400d45567643c8866e5208789e90483)/[`12eea8ed`](https://github.com/area17/twill/commit/12eea8ed7c4303dc6016b6e160567c6c691be69f)/[`bec7af63`](https://github.com/area17/twill/commit/bec7af63e271206723210ed5c81354955ed2dec1)/[`1c8567c5`](https://github.com/area17/twill/commit/1c8567c5f6675a69d09bc51e6c37f59c438c00a2)/[`aa61012a`](https://github.com/area17/twill/commit/aa61012a05e53753593bdb949adea46020a20413)/[`a80b6e3c`](https://github.com/area17/twill/commit/a80b6e3c828396150a1ac8521b5656de092febfd)/[`a85a4255`](https://github.com/area17/twill/commit/a85a4255ffef812a97dcf90cbf3a6623d32c95fa)/[`9631e7c5`](https://github.com/area17/twill/commit/9631e7c5f5189290f873be522162aa3d122922d9)/[`b1308bad`](https://github.com/area17/twill/commit/b1308bade6ad180062214718c791822927200cab)

#### OAuth login
You can enable the  new `twill.enabled.users-oauth`  feature to let your users login to the CMS using any third party service supported by Laravel Socialite. By default,  `twill.oauth.providers`  only includes  `google`, but you are free to change it or add more services to it. In the case of using Google, you would of course need to provide the following environment variables:
```
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_CALLBACK_URL=https://admin.<madewithtwill.com>/login/oauth/callback/google
```
[`d07002e3`](https://github.com/area17/twill/commit/d07002e3432374ef20581891c1cd88f7e2a36fa3)/[`afd14245`](https://github.com/area17/twill/commit/afd14245cb0499f367be2c40a0e9fa84e02dcc1e)/ [`06ec2d4e`](https://github.com/area17/twill/commit/06ec2d4e8986c9d58ce047082c3cd75e6dd4daf1)/[`be848d6e`](https://github.com/area17/twill/commit/be848d6e2a3081e9d8944ddec8ccfb16f7ad37ec)

#### JSON fields groups
It is now possible to automatically save and retrieve multiple form fields in a single JSON column in database. See [`#410`](https://github.com/area17/twill/pull/410).

[`5564e488`](https://github.com/area17/twill/commit/5564e48801d97e28c350c2250b0b3f36efa54444)/[`#452`](https://github.com/area17/twill/pull/452)/[`657e83cf`](https://github.com/area17/twill/commit/657e83cf7191f44a81a6051c04ff9a9c62cac8e1)/[`#501`](https://github.com/area17/twill/pull/501)/[`b48793af`](https://github.com/area17/twill/commit/b48793af5ea9ef8e3e54761f58402786a54881ba)/[`#541`](https://github.com/area17/twill/pull/541)/[`74926425`](https://github.com/area17/twill/commit/7492642564e65e84646e3d9f930227bdc2400540)/[`6437a073`](https://github.com/area17/twill/commit/6437a0732ff1983196ab716395d18677cab3bc45)/[`84176e44`](https://github.com/area17/twill/commit/84176e448e47d63339bf2ffa3ac9224f3ca5dc42)

#### Azure uploads
A new `endpoint_type` to support Azure storage of all uploads, exactly like when working with S3.

[`#424`](https://github.com/area17/twill/pull/424)/[`2129c084`](https://github.com/area17/twill/commit/2129c084bd5cdf14a0617006d0332090f8f9af9c)/[`#443`](https://github.com/area17/twill/pull/443)/[`d81a5b94`](https://github.com/area17/twill/commit/d81a5b9439661a1c9449f3d25a6bc1946706788a)/[`b7a89f38`](https://github.com/area17/twill/commit/b7a89f3830a25e682a996e25176d10f6ac23059d)/[`4bf2e133`](https://github.com/area17/twill/commit/4bf2e133e9841b12f92f955ab0440fed0769f202)

#### Free layout

We added a new layout to enable creating custom pages, keeping the navigation of your admin console. See our [dedicated documentation section](https://twill.io/docs/#custom-cms-pages).

[`f1f62e16`](https://github.com/area17/twill/commit/f1f62e1695b7f5ba5f6e6cce9b64d620148940a1)/[`b72b6d12`](https://github.com/area17/twill/commit/b72b6d125c419a5b5ac3fde890dc0db2ed05c341)/[`6040300e`](https://github.com/area17/twill/commit/6040300e3e376303bba1cd6d16f686013ce764e7)/[`ef4e748d`](https://github.com/area17/twill/commit/ef4e748d35435abacd793b232b864179460a18c3)/[`cc79fd83`](https://github.com/area17/twill/commit/cc79fd8388631d35520f9f3cec843ecd633b8871)/[`42869139`](https://github.com/area17/twill/commit/4286913956dca3d76656f8a0f57740755ac39031)/[`8b5271d5`](https://github.com/area17/twill/commit/8b5271d579ebc4193d15e3d13b9dd24480f1e950)/[`ca5f3ce0`](https://github.com/area17/twill/commit/ca5f3ce0196f6f9e45f4e5e8a4d80d76146345f9)

#### Automated browsers and repeaters
Clean up your repositories with this new feature that will automatically save and retrieve browser and repeaters fields for you. See [`#400`](https://github.com/area17/twill/pull/400) [`968e7da2`](https://github.com/area17/twill/commit/968e7da28f81129e43369004690e75db4b341c99).

[`#467`](https://github.com/area17/twill/pull/467)/[`f5cef179`](https://github.com/area17/twill/commit/f5cef1797570d1b8c54acf9d84998d9d1765c075)/[`89c2297c`](https://github.com/area17/twill/commit/89c2297c07ca69fa9462d1e6e5ed42024e179e42)/[`6fd64187`](https://github.com/area17/twill/commit/6fd641879e92cedcf96bed0d5969251abcff033a)

#### Destroy records in the trash
It is finally possible to destroy and bulk destroy records in the trash!

[`1b0f0070`](https://github.com/area17/twill/commit/1b0f0070ca670ae441a974150d31e82724e85e57)/[`578da43d`](https://github.com/area17/twill/commit/578da43df70a5f9ce3c0915f44ba25644adbf153)/[`a8addc71`](https://github.com/area17/twill/commit/a8addc7140dbc98b1e0b718683a1a7cf87fc143c)

#### Duplicate records

Records can now be duplicated from the listing page.

[`bb706c26`](https://github.com/area17/twill/commit/bb706c26eb666779bef5748827d6e258a626dc04)/[`a4e2d870`](https://github.com/area17/twill/commit/a4e2d8708da6fe804874f92ac1a5e6ec795b7341)/[`068b6f2c`](https://github.com/area17/twill/commit/068b6f2c98f626e0be56a3b17aa1d0098e10170b)/[`8e39f165`](https://github.com/area17/twill/commit/8e39f16564e998591ba5e417130326c5f2dafe83)/[`a32100e9`](https://github.com/area17/twill/commit/a32100e9b9fac1225e701a880e329bcafd87c4ac)/[`bcce64fd`](https://github.com/area17/twill/commit/bcce64fd4d12fb6a4827926c36cfb05728996d85)

#### i18n
![i18n](https://twill.io/docs/changelogs_media/i18n.png)

The CMS UI can now be translated. We are shipping this update with Russian and Chinese translations. CMS users can choose their preferred language in their profile. As we are updating this changelog, French, German, Dutch, Portuguese and Polish translations have already been contributed by the community.

[`#563`](https://github.com/area17/twill/pull/563)/[`#565`](https://github.com/area17/twill/pull/565)/[`#566`](https://github.com/area17/twill/pull/566)/[`de60c543`](https://github.com/area17/twill/commit/de60c543de9998fb21281e02edd1c443d7d15aa6)/[`b22e7b4b`](https://github.com/area17/twill/commit/b22e7b4b791ceef8ed67561cc47511a6d95baa03)/[`6f7d0527`](https://github.com/area17/twill/commit/6f7d05278d39f89de175fd5f5596fb863968260e)/[`2abd8160`](https://github.com/area17/twill/commit/2abd816082a5370beeab72e03084345cb99d613f)/[`7369838a`](https://github.com/area17/twill/commit/7369838a07e88cababcc22b274701e207fb41ddd)/[`af9f995d`](https://github.com/area17/twill/commit/af9f995d2d019604f32e93546888e50e79cfaee5)/[`293f74ca`](https://github.com/area17/twill/commit/293f74ca713a1ceda2f38f7590198124ac6829e4)/[`e5eca909`](https://github.com/area17/twill/commit/e5eca9098410790cd91225f89da711e934846092)/[`cb2da8c1`](https://github.com/area17/twill/commit/cb2da8c196bd83dae66f062414d6201b300f2f77)/[`07ddbdb7`](https://github.com/area17/twill/commit/07ddbdb7d43ffffa55e10c7b38545a99da84576e)/[`664cd633`](https://github.com/area17/twill/commit/664cd633e85cfaf835ef1c2c73dc53a51ce21aee)/[`6de8008b`](https://github.com/area17/twill/commit/6de8008b31c8691023a127636d02905d7a91bf38)/[`cbd50c6c`](https://github.com/area17/twill/commit/cbd50c6c3efc0ad6522ca8e86e1ce47b48cd200f)/[`871a5ac4`](https://github.com/area17/twill/commit/871a5ac419b15f8459173b0c407e67dc4a76eb4e)/[`ff59fd83`](https://github.com/area17/twill/commit/ff59fd8353f6f03d1c2a743c456e4d382d69b0f9)/[`ecab8b9e`](https://github.com/area17/twill/commit/ecab8b9e8fd215b8146612e3d61aaa598ecc09cb)/[`e5540f73`](https://github.com/area17/twill/commit/e5540f7351f6a6395415e8f0e2a045f63bdd76cb)/[`455595c7`](https://github.com/area17/twill/commit/455595c7fda67343da461e4949d1708f2099d249)/[`4e9420b1`](https://github.com/area17/twill/commit/4e9420b18e9cb48547258575836685e7ef64ce25)/[`74fb490b`](https://github.com/area17/twill/commit/74fb490bfee14c952ae3d6481dc81342031c12e9)/[`90c303b4`](https://github.com/area17/twill/commit/90c303b4a0a0f23c8824a5a93c1ea483ba6da9f9)/[`b8470635`](https://github.com/area17/twill/commit/b84706350d69f0245433d963d25f092a769770c7)/[`7e9f6d72`](https://github.com/area17/twill/commit/7e9f6d7223aaed32d22cd1f816b4f627df344af0)/[`26bbc59e`](https://github.com/area17/twill/commit/26bbc59e3385de9aef4ce492128f6ec97152f4fd)/[`8941bec9`](https://github.com/area17/twill/commit/8941bec9b2cde341c5baf1d283fa9169d026d1a4)/[`49a04f3d`](https://github.com/area17/twill/commit/49a04f3d74c74a58c1f9a1bc6a093d2752e7ae08)/[`93c206c9`](https://github.com/area17/twill/commit/93c206c934c3ae60418a166bbf775ebd00d106be)/[`fe37e5c3`](https://github.com/area17/twill/commit/fe37e5c3ed65bf07c81f8fb0669633d37cc3b344)/[`4dbc43ed`](https://github.com/area17/twill/commit/4dbc43edbb5e03caf5056be289b86787f570c191)/[`d5ce295d`](https://github.com/area17/twill/commit/d5ce295db6b9d81a8b448de594e895cf5a9ed240)/[`56e7fed2`](https://github.com/area17/twill/commit/56e7fed24e387ed380b0b2bbb147aded6a21333d)/[`f26f6a70`](https://github.com/area17/twill/commit/f26f6a705b8364ef4986763a515ffad4aa3afebb)/[`becebc53`](https://github.com/area17/twill/commit/becebc5347e51dfec0c2f66980a9c841b1b3554b)/[`01f629d8`](https://github.com/area17/twill/commit/01f629d8d2a234b7b54f75139c243233d05511c7)/[`502165e7`](https://github.com/area17/twill/commit/502165e76a07139be255aaa6341588568639a6f9)/[`71209b66`](https://github.com/area17/twill/commit/71209b6697f730c835d2523b543e45d1b934c5a0)/[`a5f097d3`](https://github.com/area17/twill/commit/a5f097d365af970dbae1bac62b5571a883b4bf00)/[`55ed36be`](https://github.com/area17/twill/commit/55ed36be455883a491ba24ff0ab083581de92684)/[`f92e93c5`](https://github.com/area17/twill/commit/f92e93c58e594e4badc89302251a6d702c8fde7b)/[`d9c70aaa`](https://github.com/area17/twill/commit/d9c70aaac83eb2340050840953e812755c5d33e3)/[`267c6672`](https://github.com/area17/twill/commit/267c66729ddb4deaf7f38614c1082068869aa686)/[`57a19985`](https://github.com/area17/twill/commit/57a19985f2c8c5b25a96bee46c8044146e76148d)/[`c5d514b1`](https://github.com/area17/twill/commit/c5d514b1d2b19b2c07f385758101a3a6f67f4707)/[`b593ad38`](https://github.com/area17/twill/commit/b593ad3873fa931d9c4c1616e7550984cce30be1)/[`abb61e38`](https://github.com/area17/twill/commit/abb61e38a6f6fdee1c1604340eb1e9d19fc2fe6b)/[`c1e59ba1`](https://github.com/area17/twill/commit/c1e59ba1070776b39f73f7ea7f2b89b5f8731a3d)/[`11a094b7`](https://github.com/area17/twill/commit/11a094b783dd119a1345fb748de2496cc97157a9)/[`05ff649b`](https://github.com/area17/twill/commit/05ff649b05bc60d0ce309abb552ee9eed3e2cff6)/[`ca27cbb4`](https://github.com/area17/twill/commit/ca27cbb4e9ad216dbc95a0f183427cc65a37defd)/[`154d0d59`](https://github.com/area17/twill/commit/154d0d598bf7f61bbee695ccd7d37d8e02ba3088)/[`8bdb2192`](https://github.com/area17/twill/commit/8bdb2192750ed52b0c9f1be47bd0b7b018a200df)/[`cb482ea8`](https://github.com/area17/twill/commit/cb482ea878f79d32c39bbf94b630f73d7dcce0ce)/[`6dcef35f`](https://github.com/area17/twill/commit/6dcef35fa400ce266ffb8c80da58071f5d7703f6)

#### Subdomain routing

Enabling the new `'twill.support_subdomain_admin_routing'` config key allows adding top level keys to Twill's navigation and dashboard modules configuration, mapping to a subdomain. This is a very simple way to implement multi-tenant CMS/sites in Twill.

A navigation array looking like the following would expose your CMS on the  `admin.subdomain1.app-url.test`  and  `admin.subdomain2.app-url.test`  urls, with its corresponding links:

```
<?php

return [
  'subdomain1' => [
    'module1' => [...],
    ...
  ],
  'subdomain2' => [
    'module2' => [...]
    ...
  ]
];

```

App name can be set per subdomain using the 'twill.app_names' configuration array. For our example above:

```
<?php

return [
  'app_names' => [
    'subdomain1' => 'App 1 name',
    'subdomain2' => 'App 2 name',
  ],
];

```

Subdomain configuration nesting also applies to the dashboard  `modules`  key.

You can also provide a custom  `block_single_layout`  per subdomain by creating a Blade file under  `resources/views/subdomain/layouts/blocks`.

[`a4bdf225`](https://github.com/area17/twill/commit/a4bdf225259fa3b3f25d9d6127acc1dcb8d52693)/[`5f2d642b`](https://github.com/area17/twill/commit/5f2d642b8966251a9ecc3829aae0b2007d17ffbb)/[`ffdbe75a`](https://github.com/area17/twill/commit/ffdbe75ae2f347e35f0302448c3c254a8e45ac03)
> 

#### Tiptap WYSIWYG
![tiptap](https://twill.io/docs/changelogs_media/tables.png)
In order to provide HTML tables support in the WYSIWYG form field, we've integrated the [Tiptap editor](https://tiptap.scrumpy.io/) with Twill. You can use it by using the new `type` option of the `wysiwyg` form field, with the `tiptap` value. You can then enable the `table` button in your `toolbarOptions`.

[`ae62d294`](https://github.com/area17/twill/commit/ae62d29400ee61e46ab83cf8faad14d39d6ab319)/[`150eb3b1`](https://github.com/area17/twill/commit/150eb3b1eae2160b914d8f1d783cc504c0c06dce)/[`754e4cce`](https://github.com/area17/twill/commit/754e4cce663e5fdc5167241c59457095014998c8)/[`cf25ee82`](https://github.com/area17/twill/commit/cf25ee8242754f1e1536da9b691ab60aa83126ef)/[`e8b450db`](https://github.com/area17/twill/commit/e8b450db4153d9d1436f32db68daf36a85311bf9)/[`6d396a16`](https://github.com/area17/twill/commit/6d396a16cacf36d7cc6840577b67aea5b02c49f1)/[`6bb6e25b`](https://github.com/area17/twill/commit/6bb6e25b060e68ff1e529c58cba4ef0ec38f1be0)/[`5f798ef6`](https://github.com/area17/twill/commit/5f798ef6c6d2fc85b7ac173916b03fd8de21c1a3)/[`bc58e7e3`](https://github.com/area17/twill/commit/bc58e7e3f9db918d58f32034f223897d9fa2f63e)/[`6323071b`](https://github.com/area17/twill/commit/6323071b269011079429e8355674156d0215db3d)/[`584a622e`](https://github.com/area17/twill/commit/584a622e3c2686aaab3e57f6be192eb100ba22ab)

#### And more...
- Add form utils component aliases [`5d5fa662`](https://github.com/area17/twill/commit/5d5fa6624006306582e3242ce1a542260b2024b2)
> @formFieldset, @formColumns, @formCollapsedFields, @formConnectedFields, @formInlineCheckboxes
- Add sideFieldset and sideFieldsets sections to form [`807674a2`](https://github.com/area17/twill/commit/807674a2e66ba41ec75fdce5ed100e7f73f11589)
- Add slot to publisher component [`f4fceac4`](https://github.com/area17/twill/commit/f4fceac470225cbbd9b3103db3e23813fb67b53e)
- Fix #90 Add maxlength option to image metadata [`54233d21`](https://github.com/area17/twill/commit/54233d215418ed99a23e62f937ca687a935e0070)
- Allow enabling svg rendering parameters in Imgix and Glide services [`e3d89bc0`](https://github.com/area17/twill/commit/e3d89bc07c3ae9acf8b53c6aa4e787761b9a49e7)
- Add disabled property in the form submitOptions [`9129e642`](https://github.com/area17/twill/commit/9129e6423ba684e6c953eaabf91274ba6715f582)
- Support custom repository in dashboard and buckets configuration [`9d3578e3`](https://github.com/area17/twill/commit/9d3578e3df3c80278dc91954db336322bbd79276)
- Expose glide configuration [`b95ca7f6`](https://github.com/area17/twill/commit/b95ca7f633df63a2dda5009d207aa00c807e99c7)
- Added get preset url for glide [`26015870`](https://github.com/area17/twill/commit/260158705944f5543b6f8aa7eb03d6ece1e21593)
- Support multi-browsers in blocks [`f6cea4e5`](https://github.com/area17/twill/commit/f6cea4e5f45873043bec1e45eb5dbbb0dd38aea8)
- Add FilterLinks on Listings and Buckets[`3a83a069`](https://github.com/area17/twill/commit/3a83a06942129d2c32baec121203ac7ab14b09d3)/[`c4c22bee`](https://github.com/area17/twill/commit/c4c22beeef2e2a0811319a674c93b4d31398ed3e)
- Add new icons [`c762ea21`](https://github.com/area17/twill/commit/c762ea219e4ffff0515a44b3e3cd868e40a0eb53)
- [`#397`](https://github.com/area17/twill/pull/397) Support Glide Presets [`6afbbf5b`](https://github.com/area17/twill/commit/6afbbf5badabdbbcd5041b7cc5c3493bfea2537c)
- [`#521`](https://github.com/area17/twill/pull/521) Added support for nested repeaters in block [`0470afda`](https://github.com/area17/twill/commit/0470afdab8c965cde485cdc4b0eff4732d2fcf5e)
- [`#526`](https://github.com/area17/twill/pull/526) Implement updateRepeater() for morphMany relations [`26a0d2de`](https://github.com/area17/twill/commit/26a0d2de55e8d7e6c4d2c6685791a24970c36e67)
- [`#528`](https://github.com/area17/twill/pull/528) Added an extra prop to repeater field to override the name [`0ce0c4cc`](https://github.com/area17/twill/commit/0ce0c4cc8ec9d4ab07d43519e274bf08738052c3)
- [`#551`](https://github.com/area17/twill/pull/551) Display and apply default filters in listings [`77eb7f23`](https://github.com/area17/twill/commit/77eb7f2325a9b7a3aa9828d8de47ed29cde06690)
- [`#562`](https://github.com/area17/twill/pull/562) Add new fieldNote option to browser, medias and files fields [`aa7a1c5d`](https://github.com/area17/twill/commit/aa7a1c5da0e51a46fec0fd506c7251de24435938)

### Fixed

- [`#371`](https://github.com/area17/twill/pull/371) Fix maps implementation when printing values from an array. [`e6a6ac2d`](https://github.com/area17/twill/commit/e6a6ac2d65221d34a8550abce3d081e94e0ef0a9)
- [`#380`](https://github.com/area17/twill/pull/380) Bug fix with undefined route name when blocks-editor is disabled [`eaacdbcc`](https://github.com/area17/twill/commit/eaacdbccf33a52bb001197b303e6d2fc2177dc92)
- [`#381`](https://github.com/area17/twill/pull/381) Add check on translatable input store to prevent duplicate field objects [`2544d059`](https://github.com/area17/twill/commit/2544d059f1f108f4164e5a45677600ff57f651d2)
- [`#385`](https://github.com/area17/twill/pull/385) Fix create superadmin user Artisan command [`2425ea37`](https://github.com/area17/twill/commit/2425ea37e7f091aec12daa034e77a80a513574c3)
- [`#390`](https://github.com/area17/twill/pull/390) Fix ignoring current email in UserRequest [`9a6a1c10`](https://github.com/area17/twill/commit/9a6a1c105ed7f1b826a29792b2c4c1537ca038da)
- [`#395`](https://github.com/area17/twill/pull/395) Fixed TTY issue for twill build command #236 [`90141c36`](https://github.com/area17/twill/commit/90141c36c4505b6e6c6bbb22741a5d04cc587918)
- [`#402`](https://github.com/area17/twill/pull/402) When hydrating an old revision, include the ID so relationship methods don't fail [`62914c81`](https://github.com/area17/twill/commit/62914c81f0d46c56ac09acca677c68ad6cde48a1)
- [`#418`](https://github.com/area17/twill/pull/418) Fix join() syntax [`57fbdaf1`](https://github.com/area17/twill/commit/57fbdaf1b52d0a923bd84420ddd981e5f4a36007)
- [`#421`](https://github.com/area17/twill/pull/421) Display error when the blocks dir is not found [`fed7b92f`](https://github.com/area17/twill/commit/fed7b92f091ce566e6bf0fae73b8dc8b1713035a)
- [`#423`](https://github.com/area17/twill/pull/423) Fix missing /js/blocks dir on twill:blocks command [`47440423`](https://github.com/area17/twill/commit/47440423004442c77498012fb8d2b3ee6727cf6e)
- [`#425`](https://github.com/area17/twill/pull/425) Fix published being sent as string on json [`1cafa780`](https://github.com/area17/twill/commit/1cafa780263e41bcb51c01e66d0da24c8213bf62)
- [`#427`](https://github.com/area17/twill/pull/427) Fix google2fa settings not being enabled [`702e31a3`](https://github.com/area17/twill/commit/702e31a3696e3d035f1f012e25b45c4e0e040705)
- [`#429`](https://github.com/area17/twill/pull/429) Fix some deprecated helpers [`2f1e3489`](https://github.com/area17/twill/commit/2f1e348986317430526e0b7b36a62c77ecc35f6e)
- [`#431`](https://github.com/area17/twill/pull/431) fixed an http & https issue from APP URL [`abb1509f`](https://github.com/area17/twill/commit/abb1509f3306046c58b88dab5325a7ad9b8f1692)
- [`#446`](https://github.com/area17/twill/pull/446) Fixed  'delete' => true not working in indexOptions, refs #289 [`81a32414`](https://github.com/area17/twill/commit/81a3241464b3dc92dcad2b7d1e7172e471f306a5)
- [`#449`](https://github.com/area17/twill/pull/449) Fix « Handle Repeater » feature compatibility with Laravel 5.6 [`7b0a275f`](https://github.com/area17/twill/commit/7b0a275f6e4e8651eb7271b6ce7df8d169ec5f22)
- [`#459`](https://github.com/area17/twill/pull/459) Wysiwyg - Counter limit (fix counter limit in Textfield) [`01151e54`](https://github.com/area17/twill/commit/01151e54b5d8552423f93cd078ce347246b7adba)
- [`#460`](https://github.com/area17/twill/pull/460) Wysiwyg - Make sure quill is ready when checking content length [`392f29c0`](https://github.com/area17/twill/commit/392f29c02dcb1b199d9d9fe40002aee94e91003f)
- [`#461`](https://github.com/area17/twill/pull/461) Forms with no Content fieldset : fix sticky publisher module and fix sticky nav sections [`9a87a2b7`](https://github.com/area17/twill/commit/9a87a2b76f47b54def41140fa506eeec389146cf)
- [`#469`](https://github.com/area17/twill/pull/469) Check the index exists before trying to save browsers in blocks [`d1c99948`](https://github.com/area17/twill/commit/d1c99948ba2a6a0f0638114e865c45ba575575e2)
- [`#475`](https://github.com/area17/twill/pull/475) Fix the admin url of an element in the browser listing [`44ad2182`](https://github.com/area17/twill/commit/44ad2182261a2758c06ed8b77c16d5ab19f6bdba)
- [`#481`](https://github.com/area17/twill/pull/481) Fix typo in docs for nested module [`764d9fc2`](https://github.com/area17/twill/commit/764d9fc2369c9bc97126f49208f7892a32996189)
- [`#484`](https://github.com/area17/twill/pull/484) Fix join() syntax in MediaLibrary/Glide.php [`40288a9f`](https://github.com/area17/twill/commit/40288a9f5d3a8957b94d199fed01717709ef0413)
- [`#485`](https://github.com/area17/twill/pull/485) Fix/destroy sub module [`ce5d88a0`](https://github.com/area17/twill/commit/ce5d88a0a810c8bb8f313104072aeb509c68d45a)
- [`#506`](https://github.com/area17/twill/pull/506) add required namespaces for Arr and Str in views and blade macros [`ff811087`](https://github.com/area17/twill/commit/ff8110871a3787169478f22d2c2fe7977a45db15)
- [`#509`](https://github.com/area17/twill/pull/509) Fix inconsistent use of integer and bigInteger on MariaDB 10.3 [`c36252de`](https://github.com/area17/twill/commit/c36252de561c706d46424276a31c1063c12f40e0)
- [`#523`](https://github.com/area17/twill/pull/523) Fixed a bug causing select field not accepting default to be false [`d1ca7681`](https://github.com/area17/twill/commit/d1ca7681b39258977c92abef156106601fc14514)
- [`#536`](https://github.com/area17/twill/pull/536) Exception handler should return json response on ajax call [`dec4714a`](https://github.com/area17/twill/commit/dec4714a1609d73cb15a4ccbc16c3054f5d4efc7)
- [`#539`](https://github.com/area17/twill/pull/539) Fix getSlug() function when locale fallback is activated [`c48ca396`](https://github.com/area17/twill/commit/c48ca39632b95c5584df438111f94459ab2c373a)
- Fix #41 – Use text columns for medias and files uuid, alt_text, caption and filename,  make alt_text nullable [`96cdebd8`](https://github.com/area17/twill/commit/96cdebd8abc88abaeeb3b866448f96c047b400be)
- Fix #504 Swap order of breadcrumbs and secondary nav [`2f9a9070`](https://github.com/area17/twill/commit/2f9a907074c2e7fb4760b1bb12f0ca59f66bb6c0)
- Apply mine filters on dashboard drafts only if revisions are enabled [`0481894d`](https://github.com/area17/twill/commit/0481894d5c968a45fc1169feb06f563e24e902c6)
- Fix activity log breaking on destroyed subjects [`d1a18490`](https://github.com/area17/twill/commit/d1a18490c7d31d38ec7aeabdadc10302cb84523f)
- Fix lang switcher border [`4ca820fa`](https://github.com/area17/twill/commit/4ca820fa7536bda01ef6a71f746b87f9b3c55d9e)
- Fix spacing issues [`dfea4805`](https://github.com/area17/twill/commit/dfea4805de1fc6cbbbd4fc1c0763e8deffc756e0)
- Fix isAjax check on VSelect component [`46a6b424`](https://github.com/area17/twill/commit/46a6b4248d7010d404a83f5df63e9530caeb9a37)
- Force assets publish on twill:update [`21e3fda9`](https://github.com/area17/twill/commit/21e3fda99f741a704e68104e91596a96cbeba788)
- Fix deeper Twill namespaces [`d64a1585`](https://github.com/area17/twill/commit/d64a15856b92cc0a1db211e7e61c14dc1e2a7e45)
- Fix typo for issue #362 [`ac76481c`](https://github.com/area17/twill/commit/ac76481cb4c1fcba0baacce9983c6eb60ca3a951)
- Fix environment requirements in docs [`40ab9d93`](https://github.com/area17/twill/commit/40ab9d93294384fb9e415d3785efd9f93ae70f61)
- Fix #290 implementation [`076ed178`](https://github.com/area17/twill/commit/076ed178bdb9fb0dd661a0aa3cd512447675320e)
> when uploading multiple files at once, the endpoint root was appended once for each file and when not providing the environment variable, null was appended
- Fix missing AWS_ROOT variable [`2a467f22`](https://github.com/area17/twill/commit/2a467f22688495a2190ab10f1de3008daedee363)
- Fix #399 dashboard repositories service container resolution [`8491bbf5`](https://github.com/area17/twill/commit/8491bbf59ae8a8b19aa299c8b4b02c08b20665a1)
- Fix regression on form notifications [`991a6212`](https://github.com/area17/twill/commit/991a6212966d139546152f812161efcdf012088a)
- Fix bulk delete warning modal reference [`b515028e`](https://github.com/area17/twill/commit/b515028ef21a573afa1649aebcdda2c53ecdbe84)
- Fix module maker migration generator [`8e7fd67f`](https://github.com/area17/twill/commit/8e7fd67f88a84d34d737fae60126d30f7cd2de47)
- Fix migration stub typo [`ea14f330`](https://github.com/area17/twill/commit/ea14f330fb2d536d738b77d27fe83035369aa04c)
- Form - Make sure $disableContentFieldset is defined [`296cb90d`](https://github.com/area17/twill/commit/296cb90d011c90a6f619277b46679894ef947589)
- Prevent LQIP generator command crashes by letting it skip on exceptions [`68d9ad6c`](https://github.com/area17/twill/commit/68d9ad6c106a330239d86f5eb4875306f42126bb)

### Improved

- [`#386`](https://github.com/area17/twill/pull/386) Refactor calling trait methods from repository [`7e45019e`](https://github.com/area17/twill/commit/7e45019e191752af16c75da47a89aaa1763130a7)
- [`#387`](https://github.com/area17/twill/pull/387) Refactor getLanguageLabelFromLocaleCode to use php intl's "Locale::getDisplayLanguage" [`e85ff145`](https://github.com/area17/twill/commit/e85ff145bdd4a6ce266f0f04314f009d81213044)
- [`#396`](https://github.com/area17/twill/pull/396) Added Logo and Badges to README [`3bb38272`](https://github.com/area17/twill/commit/3bb38272044118605203464581b1d684aa1c97bc)
- [`#398`](https://github.com/area17/twill/pull/398) Responsive filters [`b95820f4`](https://github.com/area17/twill/commit/b95820f43dd5888c9bc1d5da6640f002922237bf)
- [`#404`](https://github.com/area17/twill/pull/404) Change the way of maintaining Twill version number [`d4ba16a3`](https://github.com/area17/twill/commit/d4ba16a356bb29b290c847adb24d9920b155d4c4)
- [`#430`](https://github.com/area17/twill/pull/430) Move logic to model [`e1ac4abf`](https://github.com/area17/twill/commit/e1ac4abfab50c7ccb650175989ecafb86c339750)
- [`#442`](https://github.com/area17/twill/pull/442) Implement a Sortable function closer to the one offered by Translatable. [`e46b2318`](https://github.com/area17/twill/commit/e46b2318c01baa8a25680b560b8ff98f40812902)
- [`#445`](https://github.com/area17/twill/pull/445) Replace bcrypt() with Hash::make in CreateSuperAdmin command [`00711ede`](https://github.com/area17/twill/commit/00711ede389e16aa135d4e11877d1e42fae99ec4)
- [`#450`](https://github.com/area17/twill/pull/450) Add style for subscript into wysiwyg [`8e00ed1e`](https://github.com/area17/twill/commit/8e00ed1edc719a718c4c3489091e14124d190188)
- [`#500`](https://github.com/area17/twill/pull/500) Extend slug character support [`fe888ffe`](https://github.com/area17/twill/commit/fe888ffe8767eaefcee2ef35644efa1bfff44823)
- [`#534`](https://github.com/area17/twill/pull/534) Slug characters extended [`42071f08`](https://github.com/area17/twill/commit/42071f0882a004cc1f4d0d8c84175ee15987d4dc)
- [`#537`](https://github.com/area17/twill/pull/537) Refactored HandleRepeaters trait [`136e7123`](https://github.com/area17/twill/commit/136e712399aa6b5ed509b4a7ba163533cc10b4e0)
- [`#538`](https://github.com/area17/twill/pull/538) Refactored HandleBrowsers trait [`61a1e810`](https://github.com/area17/twill/commit/61a1e8100a8a2b23c145e73de751de4bb9678fb0)
- 2.0 version and docs updates [`f7f2aff1`](https://github.com/area17/twill/commit/f7f2aff1840b3e7cf9f46681a7868e323aee1edb)
- Allow using the content editor even if revisions are disabled [`b0095e4f`](https://github.com/area17/twill/commit/b0095e4fcf8836aca1df14489c87e7a6258f92cb)
- Add message clarifying grouped validation error messages in blocks [`e35afce6`](https://github.com/area17/twill/commit/e35afce6dcfcb52217df5e68c5bee6212d822b08)
- More 2.0 docs adjustements [`e39e029f`](https://github.com/area17/twill/commit/e39e029fffe618d38260deb44d76ddeb4052d811)
- 2.0 documentation updates [`09e22483`](https://github.com/area17/twill/commit/09e22483554b574c0137c824829ac5fedc2cd33e)
- Allow firstOrCreate to have only attributes [`01415792`](https://github.com/area17/twill/commit/0141579230e6a08063a94f841180aaa30cc1fc70)
- Use bold font in wysiwyg editor content [`bbb1bb99`](https://github.com/area17/twill/commit/bbb1bb99d5f8599666a63ad212a47648a2f5a565)
- Disable Quill warnings [`4fc8f3a7`](https://github.com/area17/twill/commit/4fc8f3a710ae53602283594351c355c377944907)
- Consider itemLabel better in browser and files form fields [`57e4d9da`](https://github.com/area17/twill/commit/57e4d9da9e8737136eeb45903aa8ebf77b917e2a)
- Button : add download and rel attributes [`cc790342`](https://github.com/area17/twill/commit/cc790342fff20dd73c2229e02492a044258c8a61)
- Button with a:href : update props and add target [`9a2cdd8c`](https://github.com/area17/twill/commit/9a2cdd8cae42ad74bae7c727c2de0472df08fded)
- CSS - set default button as inline block to avoid small visual regression [`416dcb4c`](https://github.com/area17/twill/commit/416dcb4c8d9bee357e8b020262cdbbed0bfd7a3a)
- Button update default styling so it works with ahref [`204efc6b`](https://github.com/area17/twill/commit/204efc6b87d377ca6d6548d929f2623fa2e8e4e3)
- Refactor Button component so it is using render function and can be used to display links [`993b8bee`](https://github.com/area17/twill/commit/993b8bee2a97b4fb9908e9d7d6fc692df803e029)
- Invert sorting order in ModuleRepository. [`78a36d7f`](https://github.com/area17/twill/commit/78a36d7ff4e1e6d9fa8134c77f9932d37272fe8f)

### Chore
- [`#428`](https://github.com/area17/twill/pull/428) Use 'SemVer way' of requiring packages in composer [`ad92fbf2`](https://github.com/area17/twill/commit/ad92fbf288ef71d9dc0ae162e0abbd197bb493ee)
- Update composer.json [`786d2606`](https://github.com/area17/twill/commit/786d2606e629de064d10ca12435e053bc0435aa7), [`93a2f5ab`](https://github.com/area17/twill/commit/93a2f5ab32944f0ee1d10ffb4b329cd12ee02e15), [`aef641f5`](https://github.com/area17/twill/commit/aef641f5ddb5d22ad81120b847314cf184100b84), [`1aaf8ae9`](https://github.com/area17/twill/commit/1aaf8ae9edd16afcce290c82110d6bdcd90dd429)
- Update distributed assets [`1c74628b`](https://github.com/area17/twill/commit/1c74628baf641f5acfabf8700f539ac2c3ca1d68)
- Update docs dependencies [`1990f8ca`](https://github.com/area17/twill/commit/1990f8ca2ac0b6b8c8b2e3191142bbeeeac8a91c)


## 1.2.2 (2019-08-21)

Twill just surpassed 10k [installs](https://packagist.org/packages/area17/twill/stats) and today, version 1.2.2 is available with a significant amount of improvements and bug fixes thanks to the efforts of 21 contributors: Amr Noman, Antoine Doury, Antonin Caudron, Antonio Carlos Ribeiro, Bram Mittendorff, Daniel Ramos, Dmitrii Larionov, Fernando Petrelli, Franca Winter, Gilbert Moufflet, Jarred Bishop, Lorren Gordon, Nikhil Trivedi, Pablo Barrios, Quentin Renard, Rafael Milewski, Ray Tri, Riaan Laubscher, Stevan Pavlović, Yanhao Li, Žiga Pavlin.

**Glide support for local image rendering**

[Glide](https://glide.thephpleague.com) is an open source image rendering service that integrates well with Twill and Laravel. It is a self-hosted option for local development and/or production websites and apps that have limited needs for image resizing and cropping. For image-heavy production websites and apps, we still recommend [Imgix](https://imgix.com) or a similar third party service, or at least setting up a CDN on top of your images.

The media and file libraries `local` endpoint type has also been completely reworked to work with Laravel default public storage location. Remember to run `php artisan storage:link` locally and as part of your deployment process if you are using local uploads rather than S3.

To try out Glide on a fresh Twill app, it is as simple as updating 2 environment variables:

```
MEDIA_LIBRARY_ENDPOINT_TYPE=local
MEDIA_LIBRARY_IMAGE_SERVICE=A17\Twill\Services\MediaLibrary\Glide
```

Of course, more configuration variables are available through the new `glide` key of Twill's configuration. See the default configuration [here](https://github.com/area17/twill/blob/1.2/config/glide.php).

**Making repeaters happy again**

Repeaters had a couple of issues that are now fixed in this release:

* repeaters in forms are now updating the initially created database record instead of needlessly creating a new record each time their parent model gets updated
* repeaters in blocks are now restored correctly when restoring a past revision
* medias and files fields support has been improved

**Different images per language support**

You can now globally enable the ability for your content editors to provide different images per language in the media form field using the `media_library.translated_form_fields` configuration key (defaults to `false`). The user experience is exactly the same as our other translatable field. When rendering in a template or API, you can fallback to the default language if no image has been selected for the current language.

**Cleaning up internals**

Lead by community member [Stevan Pavlović](https://github.com/stevanpavlovic), an effort to clean Twill internals begins with this release. Laravel helpers and facades are getting replaced by dependency injection or at least, for now, to avoid consequent breaking changes, by fully qualified imports.

**And a lot more in the changelog below!**

**HOW TO UPDATE**

To update, you will need to run `composer update` in your project and then run the new Twill provided artisan command: `twill:update`. This will generates new database migrations and invite you to migrate your database. Those new migrations are safe to run as they check for the existence (or inexistence) of tables and columns in your database before doing anything. If you are upgrading from an earlier version than `1.2`, you will need to update your `composer.json` file first: `"area17/twill": "1.2.*"`.

**NOTE ABOUT UPCOMING LARAVEL 6 AND SEMANTIC VERSIONING**

Laravel 6 upcoming release was [announced](https://laravel-news.com/laravel-v6-announcement) a few weeks ago at [Laracon US](https://laracon.us)! Twill will of course support it soon after the official release, which should happen at the end of August at [Laracon EU](https://laracon.eu) 🤞.

Taylor Otwell also explained why v6 instead of v5.9 since the next release is not a paradigm shift for the framework: Laravel is adopting [semantic versioning](https://semver.org/) (`major.minor.patch`) and for simplicity, we will make that shift as well.

Right now, Laravel and Twill are following [romantic versioning](http://blog.legacyteam.info/2015/12/romver-romantic-versioning/) (`paradigm.major.minor`). This is why Twill 1.2.2 is not just about patches but new features and improvements too. 

Moving forward, once Laravel 6 is released, a release with breaking changes will be considered major, which would mean Twill 2.0.0 right now. A release with new features would be 1.3.0, and patches would be 1.2.3. 

You can start using Composer's [caret version range](https://getcomposer.org/doc/articles/versions.md#caret-version-range-) (`^1.2.2`) now if you'd like to benefit from new features without fearing breaking changes on your next `composer update`! If you'd rather stick to a stricter way of requiring Twill versions (fair enough, we do that in Twill's npm dependencies for your own safety), you will have to update your `composer.json` file to get new features rather than patches only.

### Added
- Local image rendering service using Glide 🖼 (6e427fc6, e878b9af, 2a54c030, 0e8adb85)
- Support for translated medias field and extra metadatas (d16386e5, 484c3c1e, e384dad4, 5b28acf7, 4db1ff45)
- Support for maxlength counter on WYSIWYG form fields (d6301ff7, 93af3915, c916e760)
- Support for block groups (57bed474)
- Configuration option to prefix s3 uploads (#290) (b85df5ac)
- Helper to dump ready to use SQL queries (87e20508)
- Option to provide a custom static permalink under the form's title editor (f9c6ed71)
- `twill:update` command and new database migrations for 1.2.2 (b251ffa0)

### Fixed
- Fix media and file local libraries: local disk is now defined automatically by Twill, and configured to work seamlessly with Glide (10b9cc7a, 52cabe32, ff1add80, 10aa2c53, 876c93a2)
- Fix repeaters issues with restoration, update, medias and files fields support (7ec42565, 6425a3fe, c2703b25, 60a239b8, 7e348f4d)
- Fix #32: markdown based mail notifications breaking in host Laravel apps (c0239ad7)
- Fix authorization gates conflicts (d2036f29, b08b1218)
- Fix default Quill.js WYSIWYG theme rendering (e593ac6f)
- Fix browser when a selected item is deleted (5e085139)
- Fix global search input misbehavior (31fef7ce)
- Fix publish button label copy when publishing is not available (82ec2c8d)
- Fix Twill dev script console errors by disabling host check in hot script (0707f5bc)
- Fix Twill provided blocks validation rule (cc277f5e)
- Fix support for custom app namespace (#280) (eb780a5b)
- Fix canvas rendered cropped image no-cache hack (#261) (ebe4450b)
- Fix S3 uploader signature function calls (#259) (41828cd5)
- Fix missing header method exception in ValidateBackHistory middleware (#234) (2ee1080d)
- Fix media metadata helper issues (1b07f493)
- Fix some documentation typos (df870b54, a6dda857)
- Fix some styling bugs (faa4f89e, 77e4d2d0)
- Fix dashboard settings: activity option was not used (f67ca2ef)

### Improved
- Twill's CLI now automatically format the provided module name to be valid: `article`, `articles`, `Article`, you name it, will now correctly generate file for an `articles` Twill module with the correct stub replacements. (3e5d6e99)
- Forms extensibility improvements (d2f4008c, 7382c64c, a06b7a8a)
- Switch from push() to save() when creating/updating models (661e5cfd)
- Add more Language code to label mappings (#299) (d842b441)
- Support more languages in frontend slugify function (2f656287)
- Improved support for Quill.js toolbar modules on the `wysiwyg` form field (e593ac6f, ff9a8319, 3f675d27)
- Improve support for translatable.use_property_fallback (15a9331b)
- Use morphClass consistently in browsers (4ec38c2b)
- Use module controller defined scopes when counting by status (56a2d3aa)
- Code quality (replace helpers and facades by dependency injection when possible or fully qualified facades) (6f449ac2, 89687c1f, 9554a0cd, b0a3297c, 358ca416, 86192a16, 21068eb3, d443309d, 05bdfa2a, 80a0f919, 5acb7f1f, 49b2c664, 7625fb33, 1dea3d93, 6972435b, d597f713, 1de922b6, 37b4fd2a, 6092fba0, 6fe254a8, 5044c8ef, f9e2b5cd, c9ef6b52, bcc77308, 2b3f6d3f, df3650a0, a6106b7e, 6b5c49ac, d80ef94c, c889c116, 4f80c83d, 6f4e9c92)
 
### Chore
- Update composer dependencies (e1dfc11e)
- Update npm dependencies (06184c0b)
- Update docs to VuePress 1.0 (72217206)

## 1.2.1 2019-03-15

### Added
- Laravel 5.8 support (#209)
- CMS users 2 factor authentication (requires the `php-imagick` extension installed when enabled) (2753b4aa)
- Media library custom fields (181eabe3)
- Browser field with multi-types (a0804b7, e6864f4)
- Medias, select and radio fields support in settings (#87) (5ba1dcd, 8d251f1)
- Support for default values in input and wysiwyg fields (1b27210)
- Add option to keep value inside connector field when toggled (d0a92f2)
- Implement an easy way to check for images existence (#53) (19e6f8f)
- Provide a way to disable the main content fieldset in forms (862307e)
- Add wide modal option to browser form field (#105) (389ce5d)
- Enable HTML rendering in browsers (#100) (f318bb1, 9ff1bc5)
- Add a way to add pattern for the routes and domain of the admin (fbc4919)

### Fixed
- Fix medias and files form fields conflicts (#72) (adbfe66)
- Fix login error state (e55fd55)
- Fix npm scripts in documentation (5a6d368)
- Correct documentation typos (#43) (aece0a3)
- Fix reference to hard-coded twill users table name by using config value (ec9b377)
- Fix wrong parameter order in fileObject helper (#99) (1746daf)
- Fix settings for all types of translatable configurations (2570c5f)
- Fix select field value in settings for codebases with multiple languages (0936899)
- Update and block vue-select to last version (2.5.1). Update style and logic according to changes in vue-select (b3d200d)
- Fixed published scopes hook (3bfbfd0)
- Fix publication timeframe listing column (2eee60e)
- Fix CMS global search on translated titles (a5b05d3)
- Fix for non existing crop settings (ca778f6)
- Fix default locale column length Closes #80 (209e63f)
- Fix logged-in admin user privilege escalation (27cd3f8a)
- Prevent unauthorized users from accessing CMS users listing
- Fix translated file form field creating duplicate attached files after saving twice
- Fix uploader showing duplicate on upload error


### Improved
- Update to Laravel Mix 4 (#113)
- Address some install and build issues:
  - Publish compiled assets on install
  - Provide an experimental artisan based build command `php artisan twill:build`
  - Move npm documentation down as this is not needed to get started anymore, only when creating custom blocks
  - Fix npm scripts cp syntax once again, fixing #165
- Update front language components to support large number of languages (#47) (5e6c22a)
- Check database connection before twill:install (#66) (30b25be)
- Improve create super admin command (#68) (8ca8927)
- Added default false value to published column on module's default migration (#93) (21e7317)
- Wysiwyg - Default styling for the superscript (8b0e950)
- Update image styling in browser items list (b3c1103)
- Memoize translations to avoid querying the relationship multiple times when checking for active translations (d0b85be, 90c1b78)
- Languages list in listings – show first 4 only (ad434c7)
- Allow main nav to scroll on overflow-x (432b463)
- Add repeaterName parameter to repository repeater relates functions (#129) (aead7aa)
- Remove unecessary check for empty value before saving text fields into vuex store (e8866e4)
- Improve usability of the full screen content editor on mobile
- Various documentation improvements


## 1.2.0 2018-09-24

It's been an exciting first few months for Twill, and along the way, we've been listening to your [feedback](http://github.com/area17/twill/issues). Today, we're excited to release Twill 1.2 with easier setup, improved documentation, and several improvements. We also happily welcomed our first external contribution from @yanhao-li and a lot of research on extensibility from @IllyaMoskvin!

[Breaking changes](#changed) have been kept to a minimum and we've provided configuration variables for backward compatibility. 

Reminder: Twill's versioning scheme maintains the following convention: `paradigm.major.minor`, exactly like Laravel. Fun fact: this is called [Romantic Versioning](http://blog.legacyteam.info/2015/12/romver-romantic-versioning/)! When referencing Twill from your application, you should always use a version constraint such as `1.2.*`, since major releases of Twill do include breaking changes.

### Added

- Support for Laravel 5.7  (40210129, f3156836)
- Package auto-discovery for Laravel >= 5.5 (1642477)
- Documentation sources (VuePress project running at [twill.io/docs](https://twill.io/docs))
- Updated documentation sections:
  - Architecture concepts
  - Local environment requirements and installation
  - Configuration
  - Revisions and previewing
  - Dashboard
  - Global search setup
  - Settings sections 
  - Imgix sources setup
- Default CMS global search implementation (edac38ae, b234170)
- My drafts module in dashboard (70d89aa1)
- Option to enable the activity log when dashboard is disabled (3eb4b2a)
- Support for browser field in repeaters (f1f68bc)
- CMS users optional fields (title, description) (a75cb00b)

### Fixed

- Revision restoration (e87a71bd, eb9718ab, 937bbd24)
- Previewing repeaters in blocks (ffde802b, 8d9f656a, a6136cf1, e0f3e70c)
- Cropped thumbnail rendering CORS issue (f9f6896e)
- Prevent undefined formFieldValue in input and wysiwyg fields (5211a447)
- Uploader autoretry (342a79cf)
- CMS users permissions (publishers can't edit other users, admins can create new users) (1dd825c)
- CMS users profile image cropping parameters (b9e22a6a)
- Irrelevant error during Twill setup (94589134)
- Media library console errors on dashboard (e5f959a)

### Improved

- Installation process and compatibility with existing applications (0ab27de, c6353e7)
- Previews and block editor developer experience (48a7fd8)
- Blocks customization (221a03ed, 145b35b2, a10a3d6f)
- Dashboard activities labels (15e098dc)
- Update npm deps (e1a4117c)
- CRUD module generator output (bd84f41)

### Changed

- CMS users and their password reset tokens are now stored in `twill_users` and `twill_password_resets` tables by default, with options to provide custom table names in `config/twill.php` for backward compatibility (`twill.users_table` and `twill.password_resets_table`) (c6353e7)
- Twill's exception handler is bound to all controllers, with an option to opt-out for extensibility (`twill.bind_exception_handler`) (43f27de)
- Change default column in repository's listAll helper function to `title` (from `name`) (024be645)
- Configuration enables dashboard, search and buckets features by default (a02c59c). You can disable them in the `enabled` configuration array of your `config/twill.php` file.

## 1.1.0 2018-07-05

### Added

- Option to the browser field to disable sorting (228babb)
- Option to the select form field to enable search (9f49c11)
- Helper CMS image function that takes the first available attached image (3d573dc)
- Block editor custom validation rule (57cceae)
- Allow passing extra data when rendering blocks (e9a5f4d)
- Save button inside the full screen content editor (615c168)
- Configuration based CMS dashboard (81e8dfa, b1d17b3, 25f9419, 0ced2cd, 6753027, 4b125aa, b6cef76, c7a0bba)
- UI warnings when deleting from the media library, listings, and blocks from the block editor (ff8cd77)

### Fixed

- Select options escaping (7180e63)
- Form error when content revisions have no user anymore (2483120)
- Support older Debugbar version for Laravel <5.5 (2efc9ff)
- Slugs management on non translated models in a translated app (5c30e40)

### Improved

- Babel transpilation targeted browsers (13b502a)
- Refactored nested listing (9454cdf, 3b86512, 4b4ba95, 2358a1f)
- Refactored cropping logic (79ef4fc, 310fc78)
- Cleanup and linting (ee65800, d0df756, 2d77eb7, 08d94fd, d8636bc, 5813a82, b6111a2, d495b39, 65fc025)
- Show thumbnail if available in buckets and browsers (e751272, 4f0ac41)
- Preview iframe rendering issue on Chrome (b25f457)
- Relationships sync hydration: allow hydrating a custom relationship name (f9aa631)
- Bucket screen can now live under the third level of CMS navigation (9069bb8)

### Changed

- Renamed from CMS Toolkit to Twill (a30a33e, eea3abc, d395066, 93da8e2, f409597)

## 1.0.13 2018-04-20

### Fixed

- Media field: fix canvas based cropped thumbnail (make sure it is working on Chrome and Firefox, still fallbacking to the original thumbnail on Safari because of lack of support for tainted canvas with CORS)

## 1.0.12 2018-04-19

### Improved

- Case insensitive search when using Postgres (47a64c1)

### Fixed

- Buckets: custom routes save button URL (1febe6c)

## 1.0.11 2018-04-17

### Fixed

- Media field cropped thumbnail: fix Safari error (missing support for tainted canvas with CORS) (!68)

### Improved

- Media field cropped thumbnail: prevent CORS errors entirely (!68)
- Cropper component refactor (!68)
 
### Changed

- First hint of renaming introducing Twill credit in footer (b84a5f6c)

## 1.0.10 2018-04-12

### Improved

- Media field : crop is now using smart crop to detect the best crop by default (58141925)
- Media field : refresh the thumbnail of media field on the fly based on the first crop (58141925)

## 1.0.9 2018-04-10

### Improved

- Drop laravel-mix requirement on host projects (!66)
```
This is to avoid conflicts with arbitrary npm setups in hosts projects
(like a project running on Webpack 4, which is not compatible with Laravel Mix as of April 2018). 
Provided NPM scripts have been modified to use a simple copy command. 
This means the CMS build is fully independent from the project build. 
It also gives up a slight performance boost in HMR mode when developing. 
```

- Media field cropped information (2f802d8)


## 1.0.8 2018-04-05

### Added

- Ability to disable sorting on the "title" listing column (03b65a8)

### Fixed

- Content editor: non draggable list of blocks in Firefox (!65)
- Media Library: switching types quickly is creating wrong listing (!64)
- Media library: bulk tagging (60c6fe9, c6ab250, 51dfd3d, 9be8227)

### Changed

- Updated flatpicker and cropper deps to more recent versions (!63)

## 1.0.7 2018-04-03

### Added

- Support for files input in forms, blocks and repeaters (!61)
- Ability to invalidate specific paths on Cloudfront (!60)
- New block icon (text-2col) (!59)

### Fixed

- Ignore blocks in database if config for type is not available (0a4f528)

### Improved

- Refactor and hook up files library with the new single media library tab system (!61)
  * Ability to configure allowed file extensions for images and files
  * Ability to configure the file service (defaults to Disk)
  * Ability to add custom types (documentation TODO)
- Merge Cloudfront config with host application's services config (!60)
- Move AWS sdk version and region to services configuration file (!60)
- Improve global error management and session expired UX (dd3390b)
- Fix block icons position (!59)

## 1.0.6 2018-03-28

### Added

- Code highlighting module in WYSIWYG form field (`code-block` in toolbarOptions) (!56)
- @pushonce(stack:key) and @endpushonce Blade directives to push to a Blade stack, but only once (by suffixing the stack name with an arbitrary unique key) (9830ef0c)

### Fixed

- Support direct S3 upload to non-default (us-east-1) S3 regions (!58)
- Block editor option to render childs in previews (64756f0)
 
### Improved
 
- Frontend build configuration slimming down non-vendor admin assets by around 40% (auto-vendorize imports from node-modules) (!57)
- Refactor external js/css loader into a reusable util (d177d0ec)

## 1.0.5 2018-03-22

### Fixed

- Module generator hasRevisions option (3fcf7f0)

### Improved

- Misc responsive fixes for small screen (!55):
  * Cropper modal update
  * Media library : grid is showing 2 images on the same row on mobile
  * Dropdown : resize based on available space
  * Overlay header : adjust infos showing up on smaller screens
  * Listing and search results : dont show thumbnails on smaller screens

## 1.0.4 2018-03-21

### Fixed

- Module repository filter ignoring null scopes (1de874e)

## 1.0.3 2018-03-21

### Fixed

- Eventual duplicate index name from migration helpers (!54)
- Listing filters overflow after opening (18577c1)

## 1.0.2 - 2018-03-21

### Improved

- Responsive: navigation, datepickers, modals, notifications, accordeons, dropdowns, filters (!52)
- Login: footer position (!52)

### Fixed

- Datepickers with no time option: fix value update in vuex store (!53)

## 1.0.1 - 2018-03-20

### Added

- Error pages for error 419 and error 429 (page expired and too many requests) (!48)

### Improved

- Focus states, active states, accessibility (!50)
- Previewer responsiveness (!50)
- Pagination reload (no reload if same page and constraint page number input) (!50)

### Fixed

- Missing brackets to build the index name in  related table migration helper (!51)
- Color picker hue selector jumps (!49)
- NPM warning because of missing config in package.json

## 1.0.0 - 2018-03-19

### Added

- Full-screen block editor with left side editing and right side drag and drop-able previews
- Blocks can now be created as regular forms using @formField Blade directives
- Vue.js blocks components generator: php artisan twill:blocks
- Form layout helpers: collapsed fields, columns, inline checkboxes and radios, connected fields
- Listings status filters (published/mine/draft/trash)
- Per CMS user listings options saved in local storage (items per page, displayed columns)
- Bulk editing in listings (publish/feature/delete/restore)
- Display/hide columns in listings
- Support for nested listings (to use in combination with a nested set)
- Support for blocks and revisions features in module stub and generator
- Publication management: add public/private and publication timeframe options
- Ability to create and edit content in a modal when a full form page is not necessary
- Custom email template
- Color picker form field
- Bulk delete and multiple selection with shift in media library
- Replace image in media form field
- Context based image alternative text and caption
- Restore soft deleted models
- Suggested frontend controller for show and preview routes with its associated router macro (Route::moduleShowWithPreview)
- Allow custom buckets routes prefixes, default to "featured" prefix
- Support starred items in buckets
- Preview in selected language
- Preview model hydration support for multi-select, browsers and repeaters
- More image helpers
- Provide a way to transform index items collection
- Provide a hook to add custom data per index item
- Allows querying module's model through its repository
- Contributions guidelines

### Improved

- Simplified form views
- Unified form fields options
- Inline form fields validation messages
- Blocks are now their own Eloquent model and a polymorphic relationship instead of being a dead json column in each module
- Media in the media library can't be deleted in they are attached to a module or block
- Use consistant syntax for config keys
- Use database transactions in module repository operations
- Support slugs with non latin languages
- Slugs management when restoring a soft deleted model
- UI responsiveness
- Documentation

### Changed

- Redesign of every single part of the admin ui
- Admin frontend assets sources are now part of this repository (in frontend/) and consists of multiple Vue.js apps compiled using Laravel Mix
- Laravel and Vue communicates using Vuex store hydration at page load and ajax requests
- Admin assets need to be compiled by the project using this library in order to include its own blocks
- Block editor changed from SirTrevor to custom Vue.js components
- WYSIWYG editor changed from Medium Editor JS to Quill.js
- Modules don't need an index Blade view anymore, all listings options are defined in the controller
- Repeaters for inline one-to-many relationships are now created as blocks

## 0.7 - 2017-10-03

### Added

- Settings feature (831c3de, d2f76dd)
- Add a ratio selector on image fields (64edd52)
- Add optional open live site link to global nav (4ede5a2)
- Add a preview/open action to listings (a9a2821)
- Allow disabling delete in module listing (df7cf5b)
- Add a copy preview link to clipboard feature (dirty js, will clean up during redesign) (f50c03a)
- Add a way to prevent publication in listing by checking a canPublish property on the model item (347cc6a)
- Add new setting to block editor config: iframe width (defaults to 66) (5dc0461)
- Add a new links only config for medium editor fields in blocks (2eb64b5)
- Allow hints in blocks input fields (21b677f)
- Add placeholder in block text fields for simple repeater blocks based on numbers (e5042fd)
- Add a button to preview module landing with drafts (2957584)
- Add a scopes parameters to apply where conditions on forSlug helper (d6d10bb)
- Add 403 error page (9aa40d5)
- Log block errors (f9d8778)

### Fixed

- Prevent initializing already initialiazed medium editor in blocks (1742a17)
- Fix module show and preview routes (extra /) (b01c1f6)
- Fix preview links (0618bc6)
- Make sure module previews are only accessible to read only users, not disabled ones. (d92d07b)
- Fix slug input (3b25ef0)

### Improved

- Allow custom publish field name in forms (043107c)
- Improve uploader drop-zone style to align with images grid (cfd32a9)
- Wording on image attachment button: use add everywhere for consistency with blocks (e6d6918)
- Hide filters dropdowns while loading select2 styles (f2eeabe)
- Hide block editor ugly json showing in textarea before Sir Trevor loads (df2193e, adf651d)
- Improve block editor UX (show title while loading, stay on edit view if validation errors) (98b8af0)
- Change cms user publish status wording (d8177be)
- Allow raw html string as index column (useful for classic model accessor instead of presenter usage) (2c057af)

### Changed

- Soft delete slugs on delete and allow reusing deleted ones (b48b563)
- Do not force plain text pasting in block editor medium fields (a01e42f)
- Allow empty or "/" module preview route prefix for catch-all route (1dda40d)
- Wording on attachment: replace detach by remove (769d18d)
- Update CMS builds from UI Toolkit (c61fd17)
- Update composer.lock (6a567c9)
- Open preview in a new tab (f48fa99)
- Revisions and links wording (4bd3e29)

## 0.6 - 2017-10-03

### Added

- Implement revisions with preview and side by side comparison (55ebdae, e35e824, 805e6b2, c51c99c, 04d1910, 303875d, e3fb70c, 28daed4, 21dd9dd, b18276c, a31b889)
- Add feature permission for publishers and admins (84e2ae0)
- Allow custom logo partial for the CMS header (435e65e)
- Add a way to add a message on top of CRUD listings (4afefe0, c9ad242)
- Add a variable to disable secondary navigation in views (a5ee316)
- Add options to the checkbox form field to enable connected actions (5dbf3c1)
- Add a way to disable feature using a canFeature attribute in models. Defaults to true. (a63aeb1)
- Add setters to the SEO object to keep defaults if passed is empty (79f9747)
- Easy way to add links in browser fields (c0d66b3, 33540d3)
- Allow raw url in CMS navigation (6e6ac31)
- Add an option to prevent related content deletion (b3016ab)
- Add an option to show the CMS users links in the top right navbar (febd212)
- Add a repository method to find first or create (0799840)
- Add a new default config with anchor for the medium editor form field (0eea90b)
- Add a scope to order with a raw string on translations (6281f1d)
- Add a scope to order by a translated field (694d8c9)
- Let's share a published scope in the parent model (c5e83e5)
- Add a way to delete existing belongsToMany repeater elements (when not used along with a multiselect) (422a924)
- Add an option to show templates on the frontend domain and protect templates from unauthenticated eyes in production (1bf3fc7)
- Add a simple way to reuse the default blocks with custom views (36ea67f)
- Add blocks css to block editor configuration (0aa829f)
- Add a new default quote block with rich editing capabilities (4f3ecad)

### Fixed

- Fix has slug behavior when using translations with a single locale (d574b33)
- Add global css fixes to the block editor form field (4f50df9)
- Force translations index name to be shorter (76bd481)
- Fix admin host lookup in exception handler and allow view override (b49c081)
- Fix characters limits display (missing space) (642129a)
- Don't show delete column header in browsers module if delete is not allowed (5fd4830)
- Fix fields in repeater (TODO: test on other projects) (c0277a7)
- Quickly fix the Laravel 5.4.22 security fix to accomodate for our convention of not specifying the scheme in APP_URL (d541983)
- Security update: force admin url on password reset routes. (271b4bb)
- Don't load blocks css if config say we use iframes (5ce6f1a)
- Fix medium editor link only button (ce368a7)
- Fix debug config (2728b7e)

### Improved

- Improve hints display on medium textarea fields (e619967)
- Improve search and filters in browser (7380e6c)
- Retain params on listing pagination (bb41603)
- Allow custom frontend view path for errors views (e8f3822)
- CMS listing titles first letter uppercase (0f2893b)
- Fields hints improvements (238f65f)
- Browser field improvements (1be5a0d)
- Support custom button title prefix on repeaters (3debd56)
- Prevent errors and provide more ways to grab images dimensions in the Imgix service (5d9e0ca)
- Disable scrollbars on the modal frame (24e325e)
- Hide scrollbars in blocks previews (a35c919)
- Use a tinier button for repeaters (2715efb)
- Cleanup (c2875df, 6a118c2)
- Cleanup module controllers by adding default empty arrays for index and form datas (09d31eb)
- Open live site in new tab (c8543b1)

### Changed

- Switch extra_css and extra_js to Blade stacks (4741806)
- Prepended scope better be prepended (83f45c6)
- Disable CMS users image by default (143f41b)
- Make parent model abstract (eed6d21)
- Remove default button block (had no renderer) (fd7b537)
- Force capitalize model first letter in getModelRepository internal helper (f7901ac)
- Don't search for tags in search, we have a filter for it (efc05ad)
- Repository searchIn helper now bundle or where queries together to avoid conflicts with other scopes (df0973b)
- Update style of the iframe for previewing the block editor (180c69d)
- Allow calling module controller form method from child controllers (ba95fba)
- Small wording updates (ed03107)
- Use the new date picker (a15830b)
- Update base blocks and text field builder (cf8fc7b)

## 0.5 - 2017-10-02

### Added

- Implement block editor previews using iframe to prevent styles conflicts (5303259)
- Add automatic buckets from configuration (ece23a9, 72b7c6c, 98118cd, 32b011f)
- Add getItemBySlug helper function to retrieve a resource from a repository (a663cd4)
- Add required label to media and file form field (737585c)
- Add belongs_to param to the browser form field to enable single selection on browser fields (b59e129)
- Add an option to ignore fields when saving, to enable partial forms for a module (51917ac)
- Add an imageObjects method to the HasMedias trait to retrieve a collection of Image associated (06a6899)

### Fixed

- Allow https on S3 library (81982b3, 2b6b2de)
- Fix the URL of the back link when a validation error occurred (9f6d568)
- Fix browser module view resolution when using a custom module name (882bd8b)
- Force mediables ordering by id for medias form field with multiple images (8d0a3b7)

### Improved

- Turn flash message into a js notification (e2ecb13 )
- Media form field failover in case of media params changes (new crop name for example) (8a074cd)

### Changed

- Move Imgix specific params to Imgix image service implementation (b4c1aed )

## 0.4 - 2017-03-01

### Added

- Functional block editor with default blocks (b53da8fe, 6bae67a, 1ad7cfc, 24f144ad, e884f14c, d838d04, 3714396e)
- Repeater form field (105c3f22a)
- Toggle columns in module listings (6f29f20)
- Tagging support for all models (e001d5e)

### Fixed

- Toolkit module views resolution (7afcf044)
- Browser insert partial (fea42b7, 84db3a6b, e7c220f)
- Translations and slugs migration helpers (786fb06)
- Password reset routes and welcome email sending policy (f04abe21)
- Slugs params for single locale setup and custom model accessor (ecfba9924, 9788a23a)
- Paginator (custom view based since L5.3) (3d8ec24)


### Improved

- Laravel 5.4 support (2c5634cb, 4a129364)
- Date picker, allow custom options (f18bbf18)
- Resource browser: Add an option to allow a module name independent from the relationship (26368c7)
- Resource browser: Add option to pass parameters to browser from the view calling the browser (af3e06ab)
- Filters style on index view (now uses custom select style) (948df42, adcbd628, d4af4b89)
- Install/setup/module commands (dd19da3, f2ec13dc, acc2a662, b38a5f07)
- Documentation (using @joyce's feedback, thanks a lot!) (ac41d07ec)


### Changed

- Differentiate Medium editor and rich text area (3125e5e)
- Form lang switcher is now included and hidden by default (892a1da9b)
- Added an ACL param to the uploader (2298ff40)
- Update assets command now call vendor:publish on the CMS Toolkit (f75eb38)

## 0.3 - 2017-02-03

### Added

- Laravel 5.4 support (a3f7ca3, 44ccf77, a7121e2, 59301db)
- Frontend assets configuration (5bc0dfc, 281a394, 423a524, cd30daa, f1280b2, ff6f280)
- Browser for related content (bc0fba0, 595f146, e1811a3, 3dd25be)
- Nested modules (3835a75, 798118a, 9bbb77e, df06492, 48a8949, ad39dd6, c1f059f, 4fb42ab, e0bf2f6)
- Hidden form field (0d9d44b)

### Fixed

- Fix uploads on environments where config cache is enabled (178f65f)
- Prevent browser back button to get back to an authenticated page after logout (0cf4248)
- Fix date_picker fieldname (d877fa9)
- Fix checkbox partial name (40e1bca)
- Lists is deprecated (e7efee1)
- Fix resourceView blade directive (3babbcc)
- Fix non translated slugs

### Improved

- Documentation (43e8cdb)
- Config merging (be425df)
- Pluralization (4e6caf5, 3d66250)

### Changed

- Remove the need to set perPage for Sortable modules (3abf48a)

## 0.2 - 2017-01-11

### Added

- Syntaxic sugar to add like where clause in controllers filters (c032539)
- Checkbox form partial (d8e98f3)

### Improved

- Documentation (d4ef4e3, 2d289bf, 067d036, a732c4d, 21b5dfe, fa1d26a, 632ebb0, f981056, b2f38b4, a5466d2, 888897d)
- Installation (14b4507, b2dcbae, f35a730, 37232d3, ca26e7b, 366c9a2, 6b41db2, 6d65801, 96dc178, 59aef13, 4f113f8, 67d64e2, 9c89233, 6b5ed46, 576dc16, ebbeb9e, 25b5d27, f313f07)
- Module generator (7870d02, 868bb26, 380e214)
- User management (a43edfd, 315e95b, 8932206, 7abf5ef)
- Module views (7ea66d6, f866e19, e03aab6, 4841087)

### Changed

- Rename Sorts traits to HasPosition for consistency (c09c531)

## 0.1 - 2016-12-16

- Initial release
