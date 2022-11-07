# Changelog

All notable changes to `twill` will be documented in this file.

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
    - You can use php artisan twill:upgrade to automate this in your code base,
      see [UPGRADE GUIDE](https://github.com/area17/twill/blob/3.x/UPGRADE.md)

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
