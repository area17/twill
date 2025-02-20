# Changelog

All notable changes to `twill` will be documented in this file.

## 3.4.1

### Improved

- Allow media and file library disk configuration using an environement variable by [@antonioribeiro](https://github.com/antonioribeiro) in https://github.com/area17/twill/pull/2676

### Fixed

- Fix #2671: 3.4.0 regression on related browsers previews by [@Tofandel](https://github.com/Tofandel) in https://github.com/area17/twill/pull/2672
- Fix #2674: 3.4.0 regression on relation column using a one-to-one relationship by [@Tofandel](https://github.com/Tofandel) in https://github.com/area17/twill/pull/2675

## 3.4.0

### Added

- Add `searchQuery` method to controller for finer control over the search by [@Tofandel](https://github.com/Tofandel) in https://github.com/area17/twill/pull/2614
- Add `clearable` method to select form field by [@zeezo887](https://github.com/zeezo887) in https://github.com/area17/twill/pull/2581

### Improved

- Rethink the way the error handler works by [@Tofandel](https://github.com/Tofandel) in https://github.com/area17/twill/pull/2612
- Improve related save by [@Tofandel](https://github.com/Tofandel) in https://github.com/area17/twill/pull/2599
- Limits Access Key exposition to S3 storage by [@luislavena](https://github.com/luislavena) in https://github.com/area17/twill/pull/2611
- Don't load relation for each column and allow dot notation in field name for index table by [@Tofandel](https://github.com/Tofandel) in https://github.com/area17/twill/pull/2603
- Filter - Select: expand to the longest option by [@mrdoinel](https://github.com/mrdoinel) in https://github.com/area17/twill/pull/2627
- Preview: Update default width value for the mobile preview (to a more realistic value) by [@mrdoinel](https://github.com/mrdoinel) in https://github.com/area17/twill/pull/2624

### Fixed

- Fix trim function to get corresponding input by [@DCrepper](https://github.com/DCrepper) in https://github.com/area17/twill/pull/2609
- Fix published scope by [@Tofandel](https://github.com/Tofandel) in https://github.com/area17/twill/pull/2606
- Fix sync of medias and files with multiple fields by [@Tofandel](https://github.com/Tofandel) in https://github.com/area17/twill/pull/2628
- Fix positioning of the close button in media library tags by [@mrdoinel](https://github.com/mrdoinel) in https://github.com/area17/twill/pull/2626
- Fix search functionality for buckets by [@zeezo887](https://github.com/zeezo887) in https://github.com/area17/twill/pull/2661
- Fix #2650: Added parentheses to fix the order of evaluation between t… by [@HarryThe3rd](https://github.com/HarryThe3rd) in https://github.com/area17/twill/pull/2651
- Fix deleted users causes error 500 by [@Tofandel](https://github.com/Tofandel) in https://github.com/area17/twill/pull/2643
- Fix wrong crops for Blocks in `twill:refresh-crops` by [@ptrckvzn](https://github.com/ptrckvzn) in https://github.com/area17/twill/pull/2642
- Fix server error in the dashboard when a nested module has a deleted parent by [@Tofandel](https://github.com/Tofandel) in https://github.com/area17/twill/pull/2633
- Fix slugs are not created when saving models outside of Twill or when duplicating by [@Tofandel](https://github.com/Tofandel) in https://github.com/area17/twill/pull/2618
- Fix repeaters are registered without a populated item by [@Tofandel](https://github.com/Tofandel) in https://github.com/area17/twill/pull/2605
- Fix block previews don't update when browser items are added or changed by [@zeezo887](https://github.com/zeezo887) in https://github.com/area17/twill/pull/2535
- Fix weird behaviour of slugs table active column by [@zeezo887](https://github.com/zeezo887) in https://github.com/area17/twill/pull/2531
- Fix blocks take crop settings from parent model if name is the same by [@zeezo887](https://github.com/zeezo887) in https://github.com/area17/twill/pull/2542
- Use strict check for is null, as it otherwise causes empty arrays to not have any field by [@Tofandel](https://github.com/Tofandel) in https://github.com/area17/twill/pull/2604
- Rename moduleName variable in permissionModules loop by [@zeezo887](https://github.com/zeezo887) in https://github.com/area17/twill/pull/2635
- Ensure catch-all routes do not take precedence by [@ifox](https://github.com/ifox) in https://github.com/area17/twill/pull/2669

### Docs

- Update one-to-many docs to use correct Artisan command by [@daisonth](https://github.com/daisonth) in https://github.com/area17/twill/pull/2615
- Fix basic page builder guide block file reference by [@amiraezz](https://github.com/amiraezz) in https://github.com/area17/twill/pull/2630

### Translations

- Update both Portuguese from Brazil and Portugal by [@antonioribeiro](https://github.com/antonioribeiro) in https://github.com/area17/twill/pull/2602
- Improved i18n: added missing translation key (#2616) and improved German translations by [@C2H6-383](https://github.com/C2H6-383) in https://github.com/area17/twill/pull/2634

### Chores

- Upgrade GitHub Actions artefact upload to v4 by [@ifox](https://github.com/ifox)
- Bump webpack from 5.91.0 to 5.95.0 by [@dependabot](https://github.com/dependabot) in https://github.com/area17/twill/pull/2665
- Bump body-parser and express by [@dependabot](https://github.com/dependabot) in https://github.com/area17/twill/pull/2659
- Bump cookie and express by [@dependabot](https://github.com/dependabot) in https://github.com/area17/twill/pull/2664

## 3.3.1

### Fixed
- Fix dbal 4 conflict by [@ifox](https://github.com/ifox) in https://github.com/area17/twill/pull/2596

## 3.3.0

### Added

- Laravel 11 support by [@ifox](https://github.com/ifox) in https://github.com/area17/twill/pull/2473
- Add support for `medias` fields in JSON repeaters by [@zeezo887](https://github.com/zeezo887) in https://github.com/area17/twill/pull/2554
- Add support for multiple JSON repeaters (of the same type) in one form by [@zeezo887](https://github.com/zeezo887) in https://github.com/area17/twill/pull/2517
- Add support for nested module on the dashboard by [@zeezo887](https://github.com/zeezo887) in https://github.com/area17/twill/pull/2547
- Add `connectedTo` for inline repeater and documentation about `connectedTo` by [@Tofandel](https://github.com/Tofandel) in https://github.com/area17/twill/pull/2565
- Add error reporting to block rendering by [@AidasK](https://github.com/AidasK) in https://github.com/area17/twill/pull/2580
- Allow `buttonAsLink` option on inline repeaters by [@danieldevine](https://github.com/danieldevine) in https://github.com/area17/twill/pull/2522
- Allow singular capsules on navigation by [@antonioribeiro](https://github.com/antonioribeiro) in https://github.com/area17/twill/pull/2572

### Fixed

- Tags in media library are not refreshed while updating & browsing images by [@zeezo887](https://github.com/zeezo887) in https://github.com/area17/twill/pull/2523
- Clicking cancel in the block editor deletes all the unsaved blocks by [@zeezo887](https://github.com/zeezo887) in https://github.com/area17/twill/pull/2578
- Allow single deletion in form builder inline repeater by [@zeezo887](https://github.com/zeezo887) in https://github.com/area17/twill/pull/2504
- Allow deleting media after records bulk destroy by [@zeezo887](https://github.com/zeezo887) in https://github.com/area17/twill/pull/2519
- Allow case-insensitive search for translated models for postgres by [@zeezo887](https://github.com/zeezo887) in https://github.com/area17/twill/pull/2528
- 404 error when restoring revision in nested modules by [@zeezo887](https://github.com/zeezo887) in https://github.com/area17/twill/pull/2541
- Fix alt text stripping accents and single quotes by [@Tofandel](https://github.com/Tofandel) in https://github.com/area17/twill/pull/2514
- Changing twill user password in admin to use the laravel algo by [@Tofandel](https://github.com/Tofandel) in https://github.com/area17/twill/pull/2582
- Undefined variable $formBuilder error when extending form layout in custom pages by [@zeezo887](https://github.com/zeezo887) in https://github.com/area17/twill/pull/2577
- Error with time-picker when `allowInput` is true by [@zeezo887](https://github.com/zeezo887) in https://github.com/area17/twill/pull/2576
- Don't delete and recreate existing `mediables` and `fileables` by [@Tofandel](https://github.com/Tofandel) in https://github.com/area17/twill/pull/2567
- Make `twillTrans` exportable so it can be used in config by [@Tofandel](https://github.com/Tofandel) in https://github.com/area17/twill/pull/2563
- Table builder `Browser` column overrides parent module field by [@zeezo887](https://github.com/zeezo887) in https://github.com/area17/twill/pull/2506
- Disable heading extension if it's not in the Tiptap toolbar by [@Tofandel](https://github.com/Tofandel) in https://github.com/area17/twill/pull/2511
- Relation field of datatable does not allow sorting by [@zeezo887](https://github.com/zeezo887) in https://github.com/area17/twill/pull/2526
- Default login redirect to admin url by [@Tofandel](https://github.com/Tofandel) in https://github.com/area17/twill/pull/2569
- Different datetime parsing for publish date on listing and edit screen by [@zeezo887](https://github.com/zeezo887) in https://github.com/area17/twill/pull/2510
- Fix created at is null in slug table by [@Tofandel](https://github.com/Tofandel) in https://github.com/area17/twill/pull/2588

### Docs

- Fix typo by [@k-msalehi](https://github.com/k-msalehi) in https://github.com/area17/twill/pull/2564
- Change `admin.` route to `twill.` by [@LucaRed](https://github.com/LucaRed) in https://github.com/area17/twill/pull/2585
- Prettify instructions and add the capsules>list array keys to improve DX by [@antonioribeiro](https://github.com/antonioribeiro) in https://github.com/area17/twill/pull/2527
- Update twill version during installation by [@Mavv3006](https://github.com/Mavv3006) in https://github.com/area17/twill/pull/2584

### Chores

- Refactor form services to avoid repeating code by [@Tofandel](https://github.com/Tofandel) in https://github.com/area17/twill/pull/2553
- Bump express from 4.18.2 to 4.19.2 by [@dependabot](https://github.com/dependabot) in https://github.com/area17/twill/pull/2560
- Bump webpack-dev-middleware from 5.3.3 to 5.3.4 by [@dependabot](https://github.com/dependabot) in https://github.com/area17/twill/pull/2556
- Bump follow-redirects from 1.15.5 to 1.15.6 by [@dependabot](https://github.com/dependabot) in https://github.com/area17/twill/pull/2545

## 3.2.1

### Fixed
- Fix datatable and buckets filters error caused by Axios breaking changes in 0.28.0 by @zeezo887 in https://github.com/area17/twill/pull/2520


## 3.2.0

### Added
- Allow dynamic title on `InlineRepeater` by @Tofandel in https://github.com/area17/twill/pull/2493
- Add command to update morph references in Twill tables by @joyceverheije in https://github.com/area17/twill/pull/2482
- Allow behaviors to load classes without being the main model of a capsule by @Tofandel in https://github.com/area17/twill/pull/2423
- Add missing step method on Twill 3 form builder input field by @zeezo887 in https://github.com/area17/twill/pull/2496

### Fixed
- Fix error when updating a model after having enabled a new language in the config by @Tofandel in https://github.com/area17/twill/pull/2487
- Fix Twill model extensibility by @Tofandel in https://github.com/area17/twill/pull/2498
- Fix shrinking block counter by @Tofandel in https://github.com/area17/twill/pull/2501
- Fix single module browser definition by @Tofandel in https://github.com/area17/twill/pull/2474
- Fix incorrect method argument order when registering Twill capsule translation by @emanueljacob in https://github.com/area17/twill/pull/2477
- Fix preview mode when trying to preview a revision that contains a block that itself uses a blockable relation by @emanueljacob in https://github.com/area17/twill/pull/2484
- Fix custom callback of Image column by @Tofandel in https://github.com/area17/twill/pull/2494
- Fix js error if you grab a block but don't move it anywhere by @Tofandel in https://github.com/area17/twill/pull/2488
- Fix `getModulePermalinkBase` by @Tofandel in https://github.com/area17/twill/pull/2471
- Fix `getBaseNamespace` by @Tofandel in https://github.com/area17/twill/pull/2420
- Fix relative path traversal in docs dev server command by @ouuan in https://github.com/area17/twill/pull/2469
- Fix Vue draggable deprecations by @Tofandel in https://github.com/area17/twill/pull/2491
- Remove duplicate extensions already included in the TipTap starterKit by @Tofandel in https://github.com/area17/twill/pull/2486

### Improved
- Fix documentation issues reported in https://github.com/area17/twill/pull/1804
- Remove HTML 4 and srcset polyfills by @Tofandel in https://github.com/area17/twill/pull/2430
- Remove deprecated zh, ji, iw and in locales to integrate Weblate (see https://github.com/area17/twill/pull/2492)
- Bump axios from 0.27.2 to 0.28.0 by @dependabot in https://github.com/area17/twill/pull/2480

## 3.1.0

### Added
- PHP 8.3 support by @antonioribeiro in https://github.com/area17/twill/pull/2374
- Form builder connected fields support by @joyceverheije in https://github.com/area17/twill/pull/2323
- Glide image rendering service improvements for remote disks by @ifox in https://github.com/area17/twill/pull/2422
- Google Analytics 4 support in the dashboard by @lewiswharf in https://github.com/area17/twill/pull/2288
- Configurable preview breakpoints by @florrie-90 in https://github.com/area17/twill/pull/2299
- Automatically add `active` to `$translatedAttributes` by @driftingly in https://github.com/area17/twill/pull/2401
- Block component classes support in the `BlockEditor` `blocks` array by @joyceverheije in [8778ab7e](https://github.com/area17/twill/commit/8778ab7e)
- Add `excludeBlocks` option to the `BlockEditor` fields by @pauldwight in https://github.com/area17/twill/pull/2409
- Add `readOnly` support to form builder `Input` field by @zachgarwood in https://github.com/area17/twill/pull/2331
- Add `searchable` support to form builder `Select` field by @iedex in https://github.com/area17/twill/pull/2415
- Add `max` support to form builder `InlineRepeater` field by @joyceverheije in [4a773869](https://github.com/area17/twill/commit/4a773869)
- Add `direction` option to `Input` and `Wysiwyg` fields by @13twelve in https://github.com/area17/twill/pull/2295
- Add alignment buttons to `TipTap` editor by @florrie-90 in https://github.com/area17/twill/pull/2305
- Add predefined class selection to `TipTap` link component by @zipavlin in https://github.com/area17/twill/pull/2336
- Add support for `titleInBrowser` and `adminEditUrl` accessors in `browsers` by @joyceverheije in [02ac019e](https://github.com/area17/twill/commit/02ac019e), ab8635fe and 160e9165
- Add `--factory` and `--seeder` to `twill:make:module` by @driftingly in https://github.com/area17/twill/pull/2402
- Add `Link` column type to table builder by @agnonym in https://github.com/area17/twill/pull/2376
- Add `routePrefix` support to nested breadcrumbs by @yamaha252 in https://github.com/area17/twill/pull/2312
- Add DI support for Twill Block `getData` method by @Afting in https://github.com/area17/twill/pull/2292
- Allow `QuickFilter` extension by @joyceverheije in [9c15b017](https://github.com/area17/twill/commit/9c15b017)
- Configurable permissions and roles table names  by @Keania-Eric in https://github.com/area17/twill/pull/2350
- Configurable user creation with default role when using OAuth SSO by @joyceverheije in [14f8dd29](https://github.com/area17/twill/commit/14f8dd29)
- Customizable title column's label by @Phiosss in https://github.com/area17/twill/pull/2318
- Current request only tertiary nav links support by @joyceverheije in [d1c06992](https://github.com/area17/twill/commit/d1c06992)
- Multiple `nested` table columns support in the table builder by @yamaha252 and @joyceverheije in https://github.com/area17/twill/pull/2314 and 1edbfbb1
- Store position and render selected values ordered on multi selects fields by @haringsrob in https://github.com/area17/twill/pull/2204
- Update migration stub and existing migrations to use anonymous class by @driftingly in https://github.com/area17/twill/pull/2406

### Fixed
- Fix `DatePicker` date selection by @ifox in [0e751723](https://github.com/area17/twill/commit/0e751723)
- Fix Tiptap `Wysiwyg` click to focus area by @TimBlanchard in https://github.com/area17/twill/pull/2418
- Fix custom components registration by @joyceverheije in [7c233334](https://github.com/area17/twill/commit/7c233334)
- Fix position value for blocks in non-default `BlockEditor` fields by @joyceverheije in https://github.com/area17/twill/pull/2381
- Fix incorrect order of nested items slug when item is more than 2 levels deep by @pauldwight in https://github.com/area17/twill/pull/2388
- Fix `parseInternalLinks` helper issues by @avinash403 in https://github.com/area17/twill/pull/2338
- Fix to reduce excessive number of queries when using Twill 3 settings by @bonroyage in https://github.com/area17/twill/pull/2369
- Fix legacy settings in Twill 3 by @joyceverheije in https://github.com/area17/twill/pull/2417
- Fix user list errors when deleting roles by @antonioribeiro in https://github.com/area17/twill/pull/2372
- Fix build error with custom icons by @emanueljacob in https://github.com/area17/twill/pull/2392
- Fix x-position of full height crops being reset at some ratios/screen sizes by @13twelve in https://github.com/area17/twill/pull/2297
- Fix fields/blocks/repeaters spacings by @ifox in [1a93fe47](https://github.com/area17/twill/commit/1a93fe47)
- Fix active navigation check when a child module uses the same name as another module by @joyceverheije in [f22eb35a](https://github.com/area17/twill/commit/f22eb35a)
- Fix create button alignment in listings by @ifox in [006f478f](https://github.com/area17/twill/commit/006f478f)
- Fix duplication when translated media fields are enabled by @petertsoisstuff in https://github.com/area17/twill/pull/2320
- Fix `getCapsuleForModel()` for when passing a Model object by @antonioribeiro in https://github.com/area17/twill/pull/2400
- Fix `keepAlive` on connected non localized fields by @joyceverheije in [b1593c75](https://github.com/area17/twill/commit/b1593c75)
- Fix optional feature migrations publication by @Tofandel in https://github.com/area17/twill/pull/2384
- Fix related table name in migration down method by @droplister in https://github.com/area17/twill/pull/2290
- Fix scheduled filter label by @droplister in https://github.com/area17/twill/pull/2291
- Fix type of `$forceLocale` in `translatedInput()` by @pvdbroek in https://github.com/area17/twill/pull/2315
- Fix user role filter when using the standard permissions setup by @ifox in [6e893232](https://github.com/area17/twill/commit/6e893232)
- Fix vselect component to align with design by @ifox in [435c6a92](https://github.com/area17/twill/commit/435c6a92)
- Fix error when running `php artisan migrate:reset` by @NuktukDev in https://github.com/area17/twill/pull/2413
- Allow dashboard modules to wrap onto the next line by @florrie-90 in https://github.com/area17/twill/pull/2298
- Always include locale in the `mediables` pivot by @bonroyage in https://github.com/area17/twill/pull/2368
- Avoid unused data and hooks when using the default role level by @joyceverheije in [5d4060b5](https://github.com/area17/twill/commit/5d4060b5)
- Check user permission when displaying activity log items by @joyceverheije in [3526dca5](https://github.com/area17/twill/commit/3526dca5)
- Don't load `DuskServiceProvider` if Dusk doesn't exist by @Tofandel in https://github.com/area17/twill/pull/2366
- Preserve default vselect value when undefined by @bwat-dev in https://github.com/area17/twill/pull/2311
- Prevent multi select options from being selected more than once by @florrie-90 in https://github.com/area17/twill/pull/2296
- Remove block actions in settings blocks by @ifox in [0bc5e754](https://github.com/area17/twill/commit/0bc5e754)
- Support using a dot as `fieldsGroupsFormFieldNameSeparator` by @yamaha252 in https://github.com/area17/twill/pull/2277
- Turn move dropdowns off for settings blocks by @droplister in https://github.com/area17/twill/pull/2293
- Update migration helper for translations table by @sauron in https://github.com/area17/twill/pull/2327
- Updated namespace paths from `Admin` to `Twill` on custom pages by @pauldwight in https://github.com/area17/twill/pull/2317
- Use custom model configuration in the relationship morph map by @redelschaap in https://github.com/area17/twill/pull/2408
- Use `pushOnce` builtin, remove custom `pushonce` directive by @mikerockett in https://github.com/area17/twill/pull/2301
- Use `twill.admin_route_name_prefix` configuration for all internal routes by @ifox and @raymondtri in https://github.com/area17/twill/pull/2286

### Docs
- Add instructions on how to use the `browser` field with a custom pivot table by @poncianodiego and @ifox in https://github.com/area17/twill/pull/2385
- Changed example value to better reflect given example in fields group docs by @Viliasas in https://github.com/area17/twill/pull/2283
- Fix artisan command by @thecrazybob in https://github.com/area17/twill/pull/2365
- Fix typo in Building a frontend guide by @driftingly in https://github.com/area17/twill/pull/2396
- Fix typo in Content - Modules - TableBuilder by @agnonym in https://github.com/area17/twill/pull/2375
- Fix typo in create page module guide by @colegeissinger in https://github.com/area17/twill/pull/2367
- Fix wrong property name in nested modules docs by @Viliasas in https://github.com/area17/twill/pull/2282
- Updates to the docs for nested modules by @pauldwight in https://github.com/area17/twill/pull/2389

### Translations
- Added Slovenian language to translations by @Neoglyph in https://github.com/area17/twill/pull/2373
- Update Dutch translations by @lindeVW in https://github.com/area17/twill/pull/2378
- Update German translations by @florianschrottenloher-iu in https://github.com/area17/twill/pull/2411
- Add all locales with translations to the user locale selector by @ifox in [6e893232](https://github.com/area17/twill/commit/6e893232)
- Configure all locales with translations for date rendering and datepicker configuration by @ifox in [eb21fa9f](https://github.com/area17/twill/commit/eb21fa9f)

## 3.0.2

### Fixed

- Rendering of nested components blocks by @haringsrob in https://github.com/area17/twill/pull/2243
- Rendering of side form if it only contains fieldsets by @agnonym in https://github.com/area17/twill/pull/2234
- Title prefix support in component blocks by @haringsrob in https://github.com/area17/twill/pull/2252
- Icon support in component blocks by @agnonym in https://github.com/area17/twill/pull/2238
- Columns support for checkboxes and radios in form builder by @bonroyage in https://github.com/area17/twill/pull/2232
- `Options::fromArray` argument order by @bonroyage in https://github.com/area17/twill/pull/2231
- Update package generator stub by @ifox in [78cc5b5d](https://github.com/area17/twill/commit/78cc5b5dc023134356210f8c8940f77ff7745ea3)

### Improved

- Fix activity log typo by @DanielSpravtsev in https://github.com/area17/twill/pull/2264
- Fix two misspellings by @skoontastic in https://github.com/area17/twill/pull/2250
- Fix typo in create modal docs by @lostdesign in https://github.com/area17/twill/pull/2262
- Update upgrade guide by @undersound in https://github.com/area17/twill/pull/2251
- Additional explanation on migration for Input field type by @tttogo in https://github.com/area17/twill/pull/2247
- Closing bracket missing by @kerkness in https://github.com/area17/twill/pull/2245
- `withFieldSets()` expects Fieldsets object, not a direct array by @agnonym in https://github.com/area17/twill/pull/2233
- Fix 'tiwll' typo in docs by @ifox in https://github.com/area17/twill/commit/715bc6f60fe0f1f6ab2a65fb67c838c3582d7794
- Fix 'Larvel' typo in docs by @ifox in https://github.com/area17/twill/commit/8db541d716135e5a8586c50ae3b240c589fa9b05
- Fix module make commands signatures addressing PHPStan error in https://github.com/area17/twill/commit/94733f1f63f3a4f7542a3af93183664b83eea839

## 3.0.1

### Fixed

- Fix Blade components resolution (causing an issue with Laravel Jetstream) by @haringsrob in https://github.com/area17/twill/pull/2223

### Improved

- Add mobile nav to all docs site pages by @13twelve in https://github.com/area17/twill/pull/2224
- Fix WYSIWYG maxlength docs by @tttogo in https://github.com/area17/twill/pull/2226
- Form Builder docs: provide more explicit directions on Fieldsets by @tttogo in https://github.com/area17/twill/pull/2227
- Fix docs space coherence for the options by @agnonym in https://github.com/area17/twill/pull/2228

## 3.0.0

This is the final, stable release for Twill 3!

### Breaking changes

- Prefix tables with `twill_` by @aksiome
- Set tiptap as default WYSIWYG type when using blade components

### Added

- Laravel 10 support by @haringsrob
- Add `fromArray` to `options` by @aksiome
- Float min, max and step props for numeric input by @iedex
- Add `source_path_prefix` to Glide ServerFactory by @matteovg7
- Allow using 0 as min or max for numeric input by @iedex

### Fixed

- Fix phpdocs by @joyceverheije
- #2044|#1818: Improve repeater cloning. by @haringsrob
- Resolve repeater duplication issue. by @haringsrob
- Prevent erroring when permissions is not able to find disabled capsules by @antonioribeiro
- #2177: Fix renderForBlocks for columns. by @haringsrob
- Fix stretched image in browser field by @joyceverheije
- Fix duplicate action redirect route for nested parent-child modules by @agnonym
- Fix duplicate block duplicate with children by @agnonym
- Fix source edit not updateing the editor (Tiptap) by @iedex
- Fixing mistypes in ru localization by @Quarasique

### Improved

- Twill 3.0 docs updates
- Enable resend email only when user is published by @joyceverheije
- #2154: Use scope. by @haringsrob
- 2179: Avoid recreating related items. by @haringsrob
- Update Environment Requirements
- Require kalnoy/nestedset as a non-dev dependency
- Update node version in CI
- Add database table names change to upgrade guide

## 3.0.0-rc4

This is the fourth release candidate for Twill 3!

### Features

- Block crops can now be defined from block components [`#2115`](https://github.com/area17/twill/pull/2115)
- `twill:update` will now ask to run migrations [`#2107`](https://github.com/area17/twill/pull/2107)
- `Select::make()->options()` now takes a closure as well [`#2143`](https://github.com/area17/twill/pull/2143)

### Bugfixes

- Fixes various issues related to blocks [`#2124`](https://github.com/area17/twill/pull/2124)
- Allows fieldset only forms [`#2117`](https://github.com/area17/twill/pull/2117)
- GetFullUrl for link browser now replace language as well [`#2142`](https://github.com/area17/twill/pull/2142)
- Fixes return types of forms so they work properly in ide's [`#2140`](https://github.com/area17/twill/pull/2140)
- Fixes a bug which would render the wrong form if block name would overlap with internal
  names [`#2135`](https://github.com/area17/twill/pull/2135)
- Fixes an issue which caused table actions to no longer work [`#2129`](https://github.com/area17/twill/pull/2129)

## 3.0.0-rc3

This is the third (and should be last) release candidate for Twill 3!

### Notable changes

- Twill now uses Tiptap wysiwyg editor by default. If you wish to use quill you will need to update fields to use that specifically. [`#2080`](https://github.com/area17/twill/pull/2080)

### Features

- Twill now uses Tiptap by default, Tiptap has been upgrade to version 2 and now has a link button that also supports browsers. [`#2080`](https://github.com/area17/twill/pull/2080)
  - The default config for the editor has been exanded to all features it supports
- The new settings are now translatable [`#2094`](https://github.com/area17/twill/pull/2094)
- SkipCreateModal now supports the table builder correctly [`#2087`](https://github.com/area17/twill/pull/2087)
- Adds the ability to show user activity on the dashboard [`#2063`](https://github.com/area17/twill/pull/2063)

### Bugfixes

- Fixed settings accessor when used with nested blocks or repeaters.
- Fixed a check so that the media library button gets disabled correctly.
- Fixes browser endpoints no longer crash when no edit url could be build.
- Various fixes for block components.

### Other

- Vue/node has been upgrade to use all of the latest versions [`#2070`](https://github.com/area17/twill/pull/2070)

## 3.0.0-rc2

This is the second release candidate for Twill 3!

### Features

- From builder now supports inline fieldsets [`#2007`](https://github.com/area17/twill/pull/2007)
- Blocks can now be defined as a blade component `php twill:make:componentBlock blockName` [`#2007`](https://github.com/area17/twill/pull/2007)

### Bugfixes

- Fixed an issue where media tags would not save [`#2051`](https://github.com/area17/twill/pull/2051)
- Fixed an issue where conditional fields would not unset [`#2043`](https://github.com/area17/twill/pull/2043)
- Vselect now properly handles floats [`#2048`](https://github.com/area17/twill/pull/2048)
- Repeaters now properly collapse/expand [`#2037`](https://github.com/area17/twill/pull/2037)

### Docs

- Various docs updated [`#2052`](https://github.com/area17/twill/pull/2052)

## 3.0.0-rc1

The first release candidate for Twill 3!

### Features

- Form builder now supports fieldsets, side forms, columns and more [`#1963`](https://github.com/area17/twill/pull/1963)
- Blocks can now be cloned from within the editor [`#1912`](https://github.com/area17/twill/pull/1912)
- Developer experience: Added a feature that can auto-login on development environments [`#1904`](https://github.com/area17/twill/pull/1904)

### Improvements

- A new slug implementation to have more consisten slug creation [`#1897`](https://github.com/area17/twill/pull/1897)
- Various styling fixes
- Improved defaults such as svg support
- Improved documentation styling and generator

### Bugfixes

- Many issues have been resolved in since beta2.

## 3.0.0-beta2

This is a stabilization release to prepare a first stable release.

### Improvements

- New documentation system
- New guide
- Improved defaults for new projects
- Added .gitattributes to reduce package size

### Features

- No new features were introduced in this release

### Bugfixes

- Restored edit link on user profiles [`#1891`](https://github.com/area17/twill/pull/1891)
- Fixed console error on forms with permissions [`#1890`](https://github.com/area17/twill/pull/1890)
- Fixed backwards compatibility for navigation link access [`#1886`](https://github.com/area17/twill/pull/1886)

## 3.0.0-beta1

### Features

- The upgrade path is now about complete. Check [UPGRADE.MD](./UPGRADE.MD) for full instructions.
- You can now validate array contains in multiselects [#1854](https://github.com/area17/twill/pull/1854)
- Added breadcrumbs using ->setBreadcrumbs on controllers.
- Added a new command that allows to only flush the twill manifest file [#1862](https://github.com/area17/twill/pull/1862)

### Bugfixes

- Translated media and slideshows can now be disabled [#1847](https://github.com/area17/twill/pull/1847)
- Fixed an issue where block fields would not fallback in translations [#1852](https://github.com/area17/twill/pull/1852)
- Small improvement to avoid additional queries in blocks [#1853](https://github.com/area17/twill/pull/1853)
- Fixed an issue where the menu would overlay the media browser
- Fixed form builder support for singletons
- The preview function is now more open and allows interaction [#1861](https://github.com/area17/twill/pull/1861)

### Translations

- Small improvements to the french translations [#1851](https://github.com/area17/twill/pull/1851)



## 3.0.0-alpha3

### Features

- New duplicate feature. Duplicating content from the ui now is limited to basic content, browsers, blocks, files and
  media. This no longer depends on revisions being available and can be hooked into to duplicate other relations.
- The table builder now has a method ->linkToEdit() to link a cell to the modal or edit form.

### Bugfixes

- Many small regressions are fixed.
- Fixes an issue where custom icons would not compile correctly.

## 3.0.0-alpha2

### Features

- Added new setting implementation [DOCS](https://github.com/area17/twill/blob/3.x/docs/src/settings-sections/index.md) [PR](https://github.com/area17/twill/pull/1796)
- Role enum can now be swapped without changing composer.json [DOCS](https://github.com/area17/twill/blob/3.x/docs/src/user-management/index.md#extending-user-roles-and-permissions) [PR](https://github.com/area17/twill/pull/1807)
- Submit options are now configurable using the getSubmitOptions [`#1719`](https://github.com/area17/twill/pull/1719)
- Using enableDraftRevisions you can now have drafts on top of published versions [`#1725`](https://github.com/area17/twill/pull/1725)

### Bugfixes

- When having many blocks, the list is now srollable [`#1464`](https://github.com/area17/twill/pull/1464)
- Improved support for custom icons [`#1732`](https://github.com/area17/twill/pull/1732)
- Fixes a js error that occurred when cloning repeaters [`#1734`](https://github.com/area17/twill/pull/1734)
- Resolved an issue where slug models could not be found if directories were nested [`#1738`](https://github.com/area17/twill/pull/1738)
- Fixed slugs in nested modules to not rely on random sort anymore [`#1743`](https://github.com/area17/twill/pull/1743)
- Fixed an issue where the activities dashboard could show less entries [`#1764`](https://github.com/area17/twill/pull/1764)
- Fixed an issue that could cause undefined errors when using subdomain routing [`#1779`](https://github.com/area17/twill/pull/1779)
- Optimized a query in HasRelated [`#1789`](https://github.com/area17/twill/pull/1789)
- Fixed thumbnail backwards compatability for the form builder.
- Fixed custom components in build process. [`#1809`](https://github.com/area17/twill/pull/1809)
- Fixed an issue that would not render images in the block preview. [`#1797`](https://github.com/area17/twill/pull/1797)

## 3.0.0-alpha1

### Major features

- Permission management [PR](https://github.com/area17/twill/pull/1138)
  - [docs](https://github.com/area17/twill/blob/3.x/docs/src/user-management/advanced-permissions.md)
- Table builder [PR](https://github.com/area17/twill/pull/1632)
  - [docs](https://github.com/area17/twill/blob/3.x/docs/src/crud-modules/tables.md)
    - Table columns now have an OOP approach
    - Table filters now have an OOP approach
- Form builder and blade-x components instead of directives for form
  views [PR](https://github.com/area17/twill/pull/1360)
  - [docs](https://github.com/area17/twill/blob/3.x/docs/src/crud-modules/form-builder.md)
    - Old directives will continue to work, but are internally converted into components. We highly suggest to change to
      components as they might be deprecated in 4.x.
- [BREAKING] DateTimes are now fully timezone aware, if you previously added workarounds there should be removable.
  On the front-end dates will always be displayed in the browsers/systems timezone, but storage is in UTC.
- Controller options now have their own methods to change defaults [pr](https://github.com/area17/twill/pull/1716)
  - [docs](https://github.com/area17/twill/blob/3.x/docs/src/crud-modules/controllers.md#controller-setup)
- There is a whole new documentation section on
  repeaters [docs](https://github.com/area17/twill/tree/3.x/docs/src/relations) with new features:
    - Repeaters can have a browse function to select existing items
    - Repeaters can have a relation with pivot data
- Blocks can be nested to infinity (but do not do that :)) [pr](https://github.com/area17/twill/pull/1397)
  - [docs](https://github.com/area17/twill/blob/3.x/docs/src/block-editor/nested-blocks.md)

### Features

- Allow options in selects to be disabled [PR](https://github.com/area17/twill/pull/1619)
- Input masking using alpinejs [PR](https://github.com/area17/twill/pull/1605)
- Min/max/step attributes for number input fields [PR](https://github.com/area17/twill/pull/1578)
- You can now use twillTrans for block/repeater titles [PR](https://github.com/area17/twill/pull/1523)
- Administrators can now reset 2fa for other users [PR](https://github.com/area17/twill/pull/1419)
- Installable examples [PR](https://github.com/area17/twill/pull/1376)
- Revisions can now be limited [PR](https://github.com/area17/twill/pull/1479)
  - [docs](https://github.com/area17/twill/blob/3.x/docs/src/crud-modules/models.md#hasrevisions)
- Morphable browser
  fields [PR](https://github.com/area17/twill/pull/1528)
  - [docs](https://github.com/area17/twill/blob/3.x/docs/src/form-fields/browser.md#morphable-browser-fields)
- `artisan twill:module:make` now automatically creates entries in config/navigation and routes files.
- When generating modules or blocks, preview/site files can now be automatically generated.

### Refactorings

- BigInt is now always used [PR](https://github.com/area17/twill/pull/1600)
- [BREAKING] Better defaults [PR](https://github.com/area17/twill/pull/1484)
- Config file names are aligned with their config key name [PR](https://github.com/area17/twill/pull/1434)
- [BREAKING] Namespaces have been renamed from Admin to Twill [PR](https://github.com/area17/twill/pull/1388)
  - see [UPGRADE GUIDE](https://github.com/area17/twill/blob/3.x/UPGRADE.md)

### Bugfixes

- Fixed an issue if traits were in a bad order [PR](https://github.com/area17/twill/pull/1577)
- Many more...

### Other

- Greatly improved test coverage.
- Reintroduced the release command. Local building workflows should not be impacted by this.

### Documentation

- Documented capsules/packages [PR](https://github.com/area17/twill/pull/1628)
  - [docs](https://github.com/area17/twill/blob/3.x/docs/src/packages/index.md)

## Twill 2.x

For older changes in Twill 2.x please consult the [2.x changelog](https://github.com/area17/twill/blob/2.x/CHANGELOG.md)
