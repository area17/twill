# Changelog

All notable changes to `twill` will be documented in this file.

## UNRELEASED (3.1.0)

### Added
- Add connected fields support to the Twill 3 form builder by @joyceverheije in #2323
- Make preview breakpoints configurable by @florrie-90 in #2299
- Add `Link` column type for to table builder by @agnonym in #2376
- PHP 8.3 support by @antonioribeiro in #2374
- Add `routePrefix` support to nested breadcrumbs by @yamaha252 in #2312
- Add direction option to form text and WYSIWYG inputs by @13twelve in #2295
- Add read-only support to the form builder's Input field by @zachgarwood in #2331
- Make permissions and roles table names configurable by @Keania-Eric in #2350
- Support multiple nested table columns by @yamaha252 and @joyceverheije in #2314 and 1edbfbb1
- Add support for current request only tertiary nav links by @joyceverheije
- Add support for titleInBrowser and adminEditUrl accessors in browsers by @joyceverheije
- Add configuration to controls whether a user should be created or not when a new user is logging in through Oauth by @joyceverheije
- Allow QuickFilter extension by @joyceverheije

### Fixed
- Fix custom components registration by @joyceverheije in 7c233334
- Turn move dropdowns off for settings blocks by @droplister in #2293
- Fix scheduled filter label by @droplister in #2291
- Fix related table name in migration down method by @droplister in #2290
- Updated namespace paths from Admin to Twill on custom pages by @pauldwight in #2317
- Fix type of $forceLocale in translatedInput() by @pvdbroek in #2315
- Fix duplication when translated media fields are enabled by @petertsoisstuff in #2320
- Support using a dot as `fieldsGroupsFormFieldNameSeparator` by @yamaha252 in #2277
- Allow dashboard modules to wrap onto the next line by @florrie-90 in #2298
- Always include locale in the mediables pivot by @bonroyage in #2368
- Fixes to reduce excessive number of queries when using Twill 3 settings by @bonroyage in #2369
- Update migration helper for translations table by @sauron in #2327
- Fix x-position of full height crops being reset at some ratios/screen sizes by @13twelve in #2297
- Fix optional feature migrations publication by @Tofandel in #2384
- Fix build error with custom icons by @emanueljacob in #2392
- Fix position value for blocks in non-default editors by @joyceverheije in #2381
- Fix user list errors when deleting roles by @antonioribeiro in #2372
- Fix keepAlive on connected non localized fields by @joyceverheije
- Fix active navigation check when a child module uses the same name as another module by @joyceverheije
- Avoid unused data and hooks when using the default role level by @joyceverheije
- Check user permission when displaying activity log items by @joyceverheije
- Fix position value for blocks in non-default editors by @joyceverheije
- Remove block actions in settings blocks by @ifox
- Fix datepicker selection by @ifox
- Fix create button alignement in listings by @ifox

### Docs
- Fix wrong property name in 12_nested-modules.md by @Viliasas in #2282
- Fix artisan command by @thecrazybob in #2365
- Fix typo in create page module guide by @colegeissinger in #2367
- Content - Modules - TableBuilder: typo by @agnonym in #2375
- Updates to the docs for nested modules by @pauldwight in #2389
- Fix typo in 8_building-a-front-end.md by @driftingly in #2396

### Translations
- Added Slovenian language to translations by @Neoglyph in #2373
- Update dutch lang files by @lindeVW in #2378

### Chores
- Update frontend dependencies by @ifox

## 3.0.2

### Fixed

- Rendering of nested components blocks by @haringsrob in https://github.com/area17/twill/pull/2243
- Rendering of side form if it only contains fieldsets by @agnonym in https://github.com/area17/twill/pull/2234
- Title prefix support in component blocks by @haringsrob in https://github.com/area17/twill/pull/2252
- Icon support in component blocks by @agnonym in https://github.com/area17/twill/pull/2238
- Columns support for checkboxes and radios in form builder by @bonroyage in https://github.com/area17/twill/pull/2232
- `Options::fromArray` argument order by @bonroyage in https://github.com/area17/twill/pull/2231
- Update package generator stub by @ifox in https://github.com/area17/twill/commit/78cc5b5dc023134356210f8c8940f77ff7745ea3

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
