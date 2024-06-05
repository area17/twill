# Changelog

All notable changes to `twill` will be documented in this file.

## 2.13.0 (2023-04-13)

### Added

- Laravel 10 support by @antonioribeiro in https://github.com/area17/twill/pull/2155
- Allow disabling automatic navigation on a Capsule package by @antonioribeiro in https://github.com/area17/twill/pull/2113
- Allow creating blocks and repeaters inside Capsules by @antonioribeiro in https://github.com/area17/twill/pull/2082
- Singleton - Add support of capsules inside the seed method by @cambad in https://github.com/area17/twill/pull/2180

### Fixed

- Fix nested repeater being duplicated by @brunEdo in https://github.com/area17/twill/pull/2211
- Fix for VSelect component to use float values by @eudaco in https://github.com/area17/twill/pull/2024
- #1957: Fix connected fields keepalive. by @haringsrob in https://github.com/area17/twill/pull/2047
- Backport connected fields. by @haringsrob in https://github.com/area17/twill/pull/2059
- Missing .env line for disabling S3 storage by @DarthMikke in https://github.com/area17/twill/pull/2162

### Improved

- Update index.md by @alexfraundorf-com in https://github.com/area17/twill/pull/2055
- Update architecture-concepts.md by @alexfraundorf-com in https://github.com/area17/twill/pull/2054
- it-IT translation enhancements by @LucaRed in https://github.com/area17/twill/pull/1929
- it translation: fixes by @LucaRed in https://github.com/area17/twill/pull/2201

## 2.12.4 (2023-01-04)

- Revert listAll change as it may conflict with method overrides.

## 2.12.3 (2023-01-03)

- Restore laravel 5.8 support.

## 2.12.2 (2022-12-20)

### Bugfixes

- Fix long dynamic titles not showing inside fieldsets [`#2014`](https://github.com/area17/twill/pull/2014)
- Fix issue where tags could not save [`#1993`](https://github.com/area17/twill/pull/1993)

## 2.12.1 (2022-12-06)

### Bugfixes

- Fix long dynamic titles overflow on Block items [`#1975`](https://github.com/area17/twill/pull/1975)

## 2.12.0 (2022-11-30)

### Breaking change

- Route::singleton is now Route::twillSingleton [`#1961`](https://github.com/area17/twill/issues/1961)

### Features

- Checkboxes can now have notes [`#1354`](https://github.com/area17/twill/issues/1354)
- Blocks in the editor can now be cloned [`#1748`](https://github.com/area17/twill/pull/1748)

### Bugfixes

- Various bug fixes

## 2.11.0 (2022-11-09)

### Features

- When displaying a field with html after the block name, this is now stripped and cleaned [`#1843`](https://github.com/area17/twill/pull/1843)

### Other

- Added support for laravel/ui 4.x and fixes an issue with Laravel 9 aws adapter [`#1900`](https://github.com/area17/twill/pull/1900)

## 2.10.0 (2022-10-18)

### Features

- Add command to allow developers to flush only the twill-manifest.json from cache [`#1859`](https://github.com/area17/twill/pull/1859)
- Adds a ArrayContains support to connected fields [`#1848`](https://github.com/area17/twill/pull/1848)
- The preview function now allows more interaction [`#1561`](https://github.com/area17/twill/pull/1561)
- Adds disabled support to translated media and slideshow [`#1845`](https://github.com/area17/twill/pull/1845)

### Bugfixes

- Avoids additional queries when getting blocks [`#1790`](https://github.com/area17/twill/pull/1790)
- Fixed an issue where translation fallback would not work for blocks [`#1754`](https://github.com/area17/twill/pull/1754)

## 2.9.1 (2022-09-30)

### Fixes

- Fixes an issue that would allow unpublished users to continue to login [`#1833`](https://github.com/area17/twill/pull/1833)
- Fixes an issue that where custom icons were not picked up by the build process [`#1825`](https://github.com/area17/twill/pull/1825)

## 2.9.0 (2022-09-13)

### Features

- Submit options are now configurable using the getSubmitOptions [`#1719`](https://github.com/area17/twill/pull/1719)
- Using enableDraftRevisions you can now have drafts on top of published versions [`#1725`](https://github.com/area17/twill/pull/1725)

### Fixes

- When having many blocks, the list is now srollable [`#1464`](https://github.com/area17/twill/pull/1464)
- Improved support for custom icons [`#1732`](https://github.com/area17/twill/pull/1732)
- Fixes a js error that occurred when cloning repeaters [`#1734`](https://github.com/area17/twill/pull/1734)
- Resolved an issue where slug models could not be found if directories were nested [`#1738`](https://github.com/area17/twill/pull/1738)
- Fixed slugs in nested modules to not rely on random sort anymore [`#1743`](https://github.com/area17/twill/pull/1743)
- Fixed an issue where the activities dashboard could show less entries [`#1764`](https://github.com/area17/twill/pull/1764)
- Fixed an issue that could cause undefined errors when using subdomain routing [`#1779`](https://github.com/area17/twill/pull/1779)
- Optimized a query in HasRelated [`#1789`](https://github.com/area17/twill/pull/1789)

### Docs

Various small documentation updates.

## 2.8.8 (2022-06-27)

###  Features

- `->pivot` is now available when using getRelated (to get the position) [`#1687`](https://github.com/area17/twill/pull/1687)
- When updating a model via the repository, the updated model is now returned [`#1706`](https://github.com/area17/twill/pull/1706)

### Fixes

- Fixed no-cache header  [`#1695`](https://github.com/area17/twill/pull/1695)
- Fixed divider icon not showing [`#1699`](https://github.com/area17/twill/pull/1699)
- Fixed Path separators for windows in capsules [`#1702`](https://github.com/area17/twill/pull/1702)

## 2.8.7 (2022-06-10)

### Features

- Added `divider` (<hr>) support to the Quill toolbar [`#1679`](https://github.com/area17/twill/pull/1679)

### Fixes

- Fixed issue that would cause Twill ui to crash when using Quill [`#1680`](https://github.com/area17/twill/pull/1680)
- Fixed some backwards compatability issues with older Laravel versions [`#1684`](https://github.com/area17/twill/pull/1684)
- Fixed issue with browsers in the block editor [`#1689`](https://github.com/area17/twill/pull/1689)


## 2.8.6 (2022-06-03)

### Fixes

- Fixed an issue that would not cleanup removed browser items in blocks [`#1675`](https://github.com/area17/twill/pull/1675)
- Fixed an issue that would not display fields translatable in the edit modal window [`#1676`](https://github.com/area17/twill/pull/1676)

## 2.8.5 (2022-05-27)

### Fixes

- Reverts change in build folder location

## 2.8.4 (2022-05-25)

### Fixes

- Fixed console error when editing block with media in the block editor [`#1659`](https://github.com/area17/twill/pull/1659)

## 2.8.3 (2022-05-19)

### Fixes

- Media fields can now be validated in blocks [`#1648`](https://github.com/area17/twill/pull/1648)
- Internal links in quill are no longer set to target \_blank [`#1649`](https://github.com/area17/twill/pull/1649)
- Improved restoring support for nested modules [`#1590`](https://github.com/area17/twill/pull/1590)

## 2.8.2 (2022-05-06)

### Fixes

- Make site link translatable [`#1617`](https://github.com/area17/twill/pull/1617)
- Fix SQL error on twill:refresh-crops command [`#1618`](https://github.com/area17/twill/pull/1618)
- Fix locale if intl extension is missing. [`#1620`](https://github.com/area17/twill/pull/1620)
- Fix usage of quotes in placeholder [`#1621`](https://github.com/area17/twill/pull/1621)
- Fix visual issue with long notes on media [`#1612`](https://github.com/area17/twill/pull/1612)
- Change search to allow less then 3 characters [`#1613`](https://github.com/area17/twill/pull/1613)
- Add polyfill for doesntContain to ensure Laravel 7 compatability [`#1635`](https://github.com/area17/twill/pull/1635)

## 2.8.1 (2022-04-26)

### Fixes

- Fixes an error when using translated validation rules in Laravel 9 [`#1611`](https://github.com/area17/twill/pull/1611)
- Avoids a 500 error if glide base url is set to an empty string [`#1603`](https://github.com/area17/twill/pull/1603)
- Fixes an issue where repeaters and blocks would not expand on create/duplicate [`#1608`](https://github.com/area17/twill/pull/1608)

## 2.8.0 (2022-04-19)

### Added

No new features were added in the final release. Please check rc and beta tags to see a full
list of new features and fixes.

### Fixed

- Improved language label display [`#1592`](https://github.com/area17/twill/pull/1592)
- Fixed regression in translatable settings [`#1598`](https://github.com/area17/twill/pull/1598)
- Fixed capsule database path [`#1583`](https://github.com/area17/twill/pull/1583)
- Fixed activity log morph size [`#1565`](https://github.com/area17/twill/pull/1565)

### Docs

- Added repeater guide [`#1576`](https://github.com/area17/twill/pull/1576)

## 2.8.0-rc.2 (2022-04-08)

### Added

- Added option to disable repeater sorting [`#1541`](https://github.com/area17/twill/pull/1541)
- Added support for connectedBrowserField when using browsers [`#1400`](https://github.com/area17/twill/pull/1400)

### Fixed

- Changed activity log morph size to bigInt [`#1565`](https://github.com/area17/twill/pull/1565)
- Fixed leftover array accessor on capsule object [`#1574`](https://github.com/area17/twill/pull/1574)
- Fixed small visual issue with long labels [`#1566`](https://github.com/area17/twill/pull/1566)
- Fixed MorphMany saving [`#1460`](https://github.com/area17/twill/pull/1460)

### Docs

- Small improvement to the install documentations [`#1569`](https://github.com/area17/twill/pull/1569)

## 2.8.0-rc.1 (2022-04-01)

### Added

- Allows media fields to use wysiwyg [`#1540`](https://github.com/area17/twill/pull/1540)

### Fixed

- Reverted change that throws exception when crops are missing [`#1535`](https://github.com/area17/twill/pull/1535)
- Fixed issue that would cause undefined index when using octane [`#1549`](https://github.com/area17/twill/pull/1549)
- Fixed bug in singleton generator when using plural name [`#1551`](https://github.com/area17/twill/pull/1551)
- Fixed regression for resources in capsules and packages [`#1552`](https://github.com/area17/twill/pull/1552)
- Fixed Glide url without scheme [`#1545`](https://github.com/area17/twill/pull/1545)

### Docs

- Improved custom page documentation [`#1548`](https://github.com/area17/twill/pull/1548)
- Improved config documentation [`#1537`](https://github.com/area17/twill/pull/1537)
- Added documentation on how to use custom icons [`#1538`](https://github.com/area17/twill/pull/1538)

## 2.8.0-beta.2 (2022-03-15)

### Added

- Twill composer packages [`#1446`](https://github.com/area17/twill/pull/1446)
- Url field type [`#1514`](https://github.com/area17/twill/pull/1514)
- Browsers are now supported by connected fields [`#1399`](https://github.com/area17/twill/pull/1399)

### Fixed

- Fixed regression with repeaters in updated block system [`#1518`](https://github.com/area17/twill/pull/1518)
- Fixes visual issue when using limitHeight on a wysiwyg field [`#1509`](https://github.com/area17/twill/pull/1509)
- When refreshing crops, block crops are now included [`#1517`](https://github.com/area17/twill/pull/1517)

## 2.8.0-beta.1 (2022-03-03)

### Added

- Laravel 9 support [`#1243`](https://github.com/area17/twill/pull/1243)
- Added Twill block classes to support block render data, validation and more [`#1421`](https://github.com/area17/twill/pull/1421)
- Repeaters and blocks are now updated instead of recreated [`#1431`](https://github.com/area17/twill/pull/1431)
- Improved translatable fields validations [`1411`](https://github.com/area17/twill/pull/1411)


### Fixed

- Update Refresh Crops command to take in consideration the MorphMap [`#1485`](https://github.com/area17/twill/pull/1485)
- Get image size from uploaded file instead of stored file [`#1412`](https://github.com/area17/twill/pull/1412)
- Fix unauthenticated user 500 error when using custom error handler [`#1449`](https://github.com/area17/twill/pull/1449)
- Fix exception for missing repository in related browser [`#1405`](https://github.com/area17/twill/pull/1405)
- Do not show changed dialog when content is identical [`#1359`](https://github.com/area17/twill/pull/1359)
- Improved performance when many fields/languages are used [`#1350`](https://github.com/area17/twill/pull/1350)

### Documentation

- Added more guides [`#1473`](https://github.com/area17/twill/pull/1473)

## 2.7.0 (2022-02-25)

### Added

- When in debug mode and a crop is missing an exception will be shown [`#1351`](https://github.com/area17/twill/issues/1351)
- When admin account creation failed, and error is now shown [`#1114`](https://github.com/area17/twill/issues/1114)
- Add an option to set `$controlLanguagesPublication` in the listing layout that can be used to disable language
publication controll in the create modal [`#1468`](https://github.com/area17/twill/pull/1468)
- You can now control the revision label from the revisionsArray method on the model [`#1467`](https://github.com/area17/twill/pull/1467)
- Validations can now be created for repeaters [`#1156`](https://github.com/area17/twill/issues/1156)
- Max amount of repeaters can now be set from the field rather than the repeater itself [`#1433`](https://github.com/area17/twill/issues/1433)
- Added an option to automatically seed singletons [`#1456`](https://github.com/area17/twill/pull/1456)

### Fixed

- Fixed regression where slugs were not checked if unique [`#1381`](https://github.com/area17/twill/discussions/1381)
- Repeater Collapse All only works once [`#1330`](https://github.com/area17/twill/issues/1330)
- Ensure correct button messages when skipping modal on new content creation [`#1324`](https://github.com/area17/twill/issues/1324)
- Allow media to be deleted when used model is removed [`#1160`](https://github.com/area17/twill/issues/1160)
- Ensure default values are set for radios in the vuex store [`#1100`](https://github.com/area17/twill/issues/1100)
- Ensure LQIP data is always available [`#1352`](https://github.com/area17/twill/issues/1352)
- Add typecasting to select dropdown for settings support [`#1203`](https://github.com/area17/twill/issues/1203)
- Move development specific autoload components [`#1391`](https://github.com/area17/twill/pull/1391)
- Repeaters no longer expand when adding a new item [`#1461`](https://github.com/area17/twill/pull/1461)
- Notes are now rendered in media fields [`#1443`](https://github.com/area17/twill/pull/1443)
- When updated_at is empty, it now falls back to the created_at timestamp [`#766`](https://github.com/area17/twill/issues/766)
- When cloning a block, the new block is dereferenced from the original [`#1410`](https://github.com/area17/twill/pull/1410)
- Improved content scheduling [`#1307`](https://github.com/area17/twill/issues/1307)
- Fixed 500 error if google analytics data is empty [`#1470`](https://github.com/area17/twill/pull/1470)
- Fixed not all Translations were loaded within editInModal [`#1469`](https://github.com/area17/twill/pull/1469)
- Improved missing "disabled" implementations for various fields [`#836`](https://github.com/area17/twill/issues/836)

### Documentation

- Added documentation for permalinks [`#903`](https://github.com/area17/twill/issues/903) [`#1092`](https://github.com/area17/twill/pull/1092)
- Added documentation for custom media metadata [`#1031`](https://github.com/area17/twill/issues/1031)
- Added documentation for singleton modules [`#1231`](https://github.com/area17/twill/issues/1231)
- Added guide on how to customize the create modal [`#1436`](https://github.com/area17/twill/pull/1438)
- Improved documentation for field grouping [`#1214`](https://github.com/area17/twill/issues/1214)
- Added documentation for side fieldsets [`#1420`](https://github.com/area17/twill/pull/1420)
- Added documentation for the tags field [`#1375`](https://github.com/area17/twill/pull/1375)

### Translations

- Added Arabic translations [`#1323`](https://github.com/area17/twill/pull/1323)
- Added Bosnian translations [`#1374`](https://github.com/area17/twill/pull/1374)
- Updated czech translations [`#1466`](https://github.com/area17/twill/pull/1466)

## 2.6.0 (2021-12-21)

### Added

- Artisan command `twill:make:singleton` to generate single-record modules [`#1178`](https://github.com/area17/twill/pull/1178)
- Option `--hasNesting` to generate self-nested modules [`#1140`](https://github.com/area17/twill/pull/1140) [`#1222`](https://github.com/area17/twill/pull/1222)
- Artisan command `twill:refresh-crops` to generate missing crops [`#1289`](https://github.com/area17/twill/pull/1289) [`8b1e4f6d`](https://github.com/area17/twill/commit/8b1e4f6d)
- TwicPics image service [`#1217`](https://github.com/area17/twill/pull/1217) [`ce15c4a5`](https://github.com/area17/twill/commit/ce15c4a5)
- Turkish language support [`#1134`](https://github.com/area17/twill/pull/1134)
- Support for translated permalinks in the title editor [`#1092`](https://github.com/area17/twill/pull/1092)
- Support for capsule service providers [`#1212`](https://github.com/area17/twill/pull/1212)
- Auto detect latitude-longitude value in location field [`#1275`](https://github.com/area17/twill/pull/1275) [`#1170`](https://github.com/area17/twill/pull/1170)
- Adds the ability to display an indexColumn selected from a relatedBrowser [`#1302`](https://github.com/area17/twill/pull/1302)

### Fixed

- 🚨 Fix CSRF vulnerability in logout method [`#1240`](https://github.com/area17/twill/pull/1240) [`29041f07`](https://github.com/area17/twill/commit/29041f07)
- Support attribute casting on model and translations with JSON field groups [`#1151`](https://github.com/area17/twill/pull/1151)
- Support dynamic repeater titles with JSON repeaters [`#1171`](https://github.com/area17/twill/pull/1171)
- Fix admin HTTP exception views detection [`#1213`](https://github.com/area17/twill/pull/1213)
- Prevent conflicts with built-in Vue component names [`#1164`](https://github.com/area17/twill/pull/1164)
- Add fallback to capsule model in permalink base [`#1216`](https://github.com/area17/twill/pull/1216)
- Add `doctrine/dbal` 3.0 support [`#1226`](https://github.com/area17/twill/pull/1226)
- Ensure capsule autoloading when config is cached [`#1242`](https://github.com/area17/twill/pull/1242)
- Fix edit link feature regression in Quill WYSIWYG [`#1270`](https://github.com/area17/twill/pull/1270)
- Remove references to deprecated Symfony class [`#1269`](https://github.com/area17/twill/pull/1269)
- Prevent undefined route errors in IconsController [`#1268`](https://github.com/area17/twill/pull/1268)
- Fix typo in `twill:capsule:install` command [`#1290`](https://github.com/area17/twill/pull/1290)
- Initialize undefined crops in cropper UI using first available ratio [`#1258`](https://github.com/area17/twill/pull/1258)
- Fix `byKey()` setting query when passing `section` argument [`#1303`](https://github.com/area17/twill/pull/1303)
- Remove duplicate test stub class [`#1311`](https://github.com/area17/twill/pull/1311)
- Fix destroy action on single nested items [`#1304`](https://github.com/area17/twill/pull/1304)
- Prevent multiple AJAX requests in Block Editor previews [`#1282`](https://github.com/area17/twill/pull/1282)
- Fix singleton routing for primary and secondary navigation support [`#1325`](https://github.com/area17/twill/pull/1325)
- Use case insensitive like operator in filterHandleTranslations for PostgresSQL support [`#1322`](https://github.com/area17/twill/pull/1322)

### Improved

- Update routes mapping order to allow overrides [`#1133`](https://github.com/area17/twill/pull/1133)
- Handle many-to-many relations in index columns [`#1174`](https://github.com/area17/twill/pull/1174)
- Add `capsule_repository_prefix` configuration [`#1209`](https://github.com/area17/twill/pull/1209)
- Support custom port in `dev_mode_url` configuration [`#1163`](https://github.com/area17/twill/pull/1163)
- Support additional table actions in module listing [`#1202`](https://github.com/area17/twill/pull/1202)
- Show red input count only if above 90% capacity [`#1237`](https://github.com/area17/twill/pull/1237)
- Collapse repeater blocks on page load [`#1296`](https://github.com/area17/twill/pull/1296)
- Update nested module count pluralisation [`#1251`](https://github.com/area17/twill/pull/1251)
- Support `titleKey` parameter in `relatedBrowsers` configuration [`#1301`](https://github.com/area17/twill/pull/1301)
- Update `HasSlug` to use Eloquent instead of DB facade [`#1309`](https://github.com/area17/twill/pull/1309)
- Update german translations [`#1235`](https://github.com/area17/twill/pull/1235)
- Update italian translations [`#1312`](https://github.com/area17/twill/pull/1312)
- Update docblock annotations [`#1167`](https://github.com/area17/twill/pull/1167/files)
- Update documentation [`#1165`](https://github.com/area17/twill/pull/1165) [`#1244`](https://github.com/area17/twill/pull/1244) [`#1236`](https://github.com/area17/twill/pull/1236)
- Add pagination and a few more quality of life updates to the documentation [`#1131`](https://github.com/area17/twill/pull/1131)

### Chores

- Update npm dependencies in documentation [`377e5e0`](https://github.com/area17/twill/commit/377e5e0b27916861caa448ef899ea0e3fbeff648)
- Bump axios from 0.21.1 to 0.21.2 [`#1327`](https://github.com/area17/twill/pull/1327)


## 2.5.3 (2021-11-26)

### Fixed

- 🚨 Fix CSRF vulnerability in logout method [`6ced7fd8`](https://github.com/area17/twill/commit/6ced7fd8) [`7477f4a3`](https://github.com/area17/twill/commit/7477f4a3) [`01150269`](https://github.com/area17/twill/commit/01150269) [`5cded9fc`](https://github.com/area17/twill/commit/5cded9fc) [`ac770b87`](https://github.com/area17/twill/commit/ac770b87)


## 2.5.2 (2021-09-16)

### Fixed

- 🚨 Fix XSS security vulnerability [#1157](https://github.com/area17/twill/pull/1157) [`2dd77b15`](https://github.com/area17/twill/commit/2dd77b15)
- Hydrate related browsers on preview [#1130](https://github.com/area17/twill/pull/1130)
- Use configured namespace when generating model class in repository [`80e1b590`](https://github.com/area17/twill/commit/80e1b590)
- Translation key typo on the dashboard [`c630d0d1`](https://github.com/area17/twill/commit/c630d0d1)

### Improved

- Call `view:clear` when updating assets with `twill:update` [`c5b96010`](https://github.com/area17/twill/commit/c5b96010)
- Add security policy file [`55b2dfd63`](https://github.com/area17/twill/commit/55b2dfd63)


## 2.5.1 (2021-09-02)

### Fixed

- Twill custom error views and ability to extend its exceptions handler [`312b44`](https://github.com/area17/twill/commit/312b446cc6f4826bf6f9d703e02fb6b96bbb2a9f)[`c16d2e`](https://github.com/area17/twill/commit/c16d2e9374dbe5c225a97910e5b228a549b887de)


## 2.5.0 (2021-09-01)

### Added

- **Block editor updates**
  - Ability to use multiple `block_editor` fields [`#918`](https://github.com/area17/twill/pull/918)
  - Dynamic block/repeater titles [`#1096`](https://github.com/area17/twill/pull/1096)
  - Update content editor sidebar layout and add new icons [`#1072`](https://github.com/area17/twill/pull/1072)
  - Update default button variant for inner repeaters [`#1073`](https://github.com/area17/twill/pull/1073)
- **Form fields updates**
  - Border option for 'radios' and 'checkbox'/'checkboxes' field [`a0376466`](https://github.com/area17/twill/commit/a0376466) [`04261d61`](https://github.com/area17/twill/commit/04261d61) [`1eda5f0b`](https://github.com/area17/twill/commit/1eda5f0b) [`93aeb570`](https://github.com/area17/twill/commit/93aeb570)
  - Columns option for 'radios', 'select', 'checkboxes' and 'multi_select' fields [`9fedcf47`](https://github.com/area17/twill/commit/9fedcf47) [`ee1f6681`](https://github.com/area17/twill/commit/ee1f6681) [`cad812c2`](https://github.com/area17/twill/commit/cad812c2)
  - Time picker form field [`42d1880a`](https://github.com/area17/twill/commit/42d1880adccdc0092188f099a4ec3b1a6d954745) [`969e800c`](https://github.com/area17/twill/commit/969e800c)
  - Option to make multi select searchable [`bbead399`](https://github.com/area17/twill/commit/bbead399d28a6ed295694e00a317d154fff62799)
  - Automatic input direction for RTL languages [`e8a60d0f`](https://github.com/area17/twill/commit/e8a60d0f) [`318834c8`](https://github.com/area17/twill/commit/318834c8) [`2b183493`](https://github.com/area17/twill/commit/2b183493)
  - Click-to-place on the Location field's map [`e48245aa`](https://github.com/area17/twill/commit/e48245aa)
  - Support inverse HasOne relationship for BelongsTo browsers [`d1b67fa7`](https://github.com/area17/twill/commit/d1b67fa7) [`f1e6efc1`](https://github.com/area17/twill/commit/f1e6efc1) [`4f791c66`](https://github.com/area17/twill/commit/4f791c66)
- **Capsules updates**
  - Autoloading system for Capsules [`cec70d03`](https://github.com/area17/twill/commit/cec70d0390bbe6c383983042cd7bf0268e15fc50)
  - Translations files for Capsules [`8f06ea53`](https://github.com/area17/twill/commit/8f06ea53928c3f83735b2068efbc13e91ecf5e76)
- **General updates**
  - SVG support with Glide [`#985`](https://github.com/area17/twill/pull/985)
  - Configurable admin routes prefix [`5e5b5a81`](https://github.com/area17/twill/commit/5e5b5a81)
  - Allow configuring password broker and enable throttle [`b421561a`](https://github.com/area17/twill/commit/b421561a)
  - Ukrainian language [`c2c08603`](https://github.com/area17/twill/commit/c2c08603)
  - Watch for custom blocks & components in development [`#1107`](https://github.com/area17/twill/pull/1107)
  - Allow setting `dev_assets_path` from `.env` [`814ade6b`](https://github.com/area17/twill/commit/814ade6b063d9e8ccc4e38d5f0e3ac907922325c)


### Fixed
- Prevent submitting a form before saving an input into the store [`#1030`](https://github.com/area17/twill/pull/1030)
- Prevent multiple submit events in add/create modals [`3dfb0c8f`](https://github.com/area17/twill/commit/3dfb0c8fe0e410e81d922facc1b2465aeb784c44) [`cb38ba53`](https://github.com/area17/twill/commit/cb38ba53f71bed61ec09b955b9d738306163afde) [`40248552`](https://github.com/area17/twill/commit/402485521520682bf9cfe1e5dcee0974ca8fe540)
- Fix incorrect position of link edit options in wysiwyg editor when height limit is set [`f8276462`](https://github.com/area17/twill/commit/f8276462)
- Ensure pasting content in quill do not make editor scroll to the top [`07f7aa00`](https://github.com/area17/twill/commit/07f7aa00)[`51302a15`](https://github.com/area17/twill/commit/51302a15)
- Safari form submit issue in Filter component [`29a1f227`](https://github.com/area17/twill/commit/29a1f227) [`91390ac2`](https://github.com/area17/twill/commit/91390ac2)
- Support revisions preview and restore with belongsTo browsers [`#984`](https://github.com/area17/twill/pull/984) [`#1085`](https://github.com/area17/twill/pull/1085)
- Toggle media library unused checkbox on clear only if active [`d73ff3eb`](https://github.com/area17/twill/commit/d73ff3ebfe77657f505d158e9431e392f29886b1)
- Order files by fileable id [`d7df01bf`](https://github.com/area17/twill/commit/d7df01bfcbf1cfe646b0259de020a3aa1f5de600)
- Register capsules routes before Twill internal routes [`c1acb981`](https://github.com/area17/twill/commit/c1acb981b3ef2a9e12d835db99dfd1a9b9429abe)
- Issues when extending Twill's exceptions handler [`itca7a650e`](https://github.com/area17/twill/commit/itca7a650e)
- Support multi-country locales on Translatable [`c5b341d4`](https://github.com/area17/twill/commit/c5b341d4) [`5d9c9953`](https://github.com/area17/twill/commit/5d9c9953)
- Fix Exception Handler broken for Laravel 8 [`569ce2e6`](https://github.com/area17/twill/commit/569ce2e6) [`ce58aae5`](https://github.com/area17/twill/commit/ce58aae5) [`76890ffa`](https://github.com/area17/twill/commit/76890ffa)


### Improved

- Update browser documentation [`b7e288db`](https://github.com/area17/twill/commit/b7e288db743d7d8756f43422b86dde0a6f274376) [`bd9f7aeb`](https://github.com/area17/twill/commit/bd9f7aebdfa809ccc630faa117db7be021d50a1c) [`1f9127b8`](https://github.com/area17/twill/commit/1f9127b8fcc5ea6fde4268184c87d22ca289f1e6) [`71a505f9`](https://github.com/area17/twill/commit/71a505f96957a8134257c7cbe81db28757e2f425) [`4e04639d`](https://github.com/area17/twill/commit/4e04639dbab64cba7eb4ce81914e9433286c3d7f)
- Update repeater field documentation [`f32ddc1a`](https://github.com/area17/twill/commit/f32ddc1a2d98cf9ec5ae83e937550bc8a2b91771) [`8f58d422`](https://github.com/area17/twill/commit/8f58d422ab80a94aeb4ce96e9134c9c63701a083) [`03dcfbd8`](https://github.com/area17/twill/commit/03dcfbd88818e7ce56e1b5985c305e5852efc8fd) [`22dfaa49`](https://github.com/area17/twill/commit/22dfaa49bd47cb5c54c0747dbacd2ed09350b2ee) [`f252727d`](https://github.com/area17/twill/commit/f252727d6bc213642ef1d06050ecc31ccabe9132)
- Add HandleRelatedBrowsers to ModuleRepository [`4c3db071`](https://github.com/area17/twill/commit/4c3db071a4655cfb0cf5d4f5911521a3ccc255c8)
- Update french lang file [`9517ff44`](https://github.com/area17/twill/commit/9517ff4443fa512a36b3b7e9ac4a34b0e6bf4013)
- Display publishable languages labels using the currently set locale [`e4dcfe3d`](https://github.com/area17/twill/commit/e4dcfe3d8dba5eb5f26de473e43ee7c421179a91)
- Move icon svg files to blade files [`cf73e2fe`](https://github.com/area17/twill/commit/cf73e2fe995210285753bc1632a0d84341591b6d)
- Update model stub default crops [`9ab7977c`](https://github.com/area17/twill/commit/9ab7977c9b1ca76e46b563955d048885544c6623) [`4ccca727`](https://github.com/area17/twill/commit/4ccca727d7ee766d627b3efe2ef0a96b32d9d618)
- Add an axios global error handler to warn on session expired [`c626a143`](https://github.com/area17/twill/commit/c626a143) [`ed6164dc`](https://github.com/area17/twill/commit/ed6164dc) [`3c78c933`](https://github.com/area17/twill/commit/3c78c933)
- Prevent console errors when using custom admin pages [`e1f606d6`](https://github.com/area17/twill/commit/e1f606d6)
- Missing lang keys [`e408fa77`](https://github.com/area17/twill/commit/e408fa77) [`14f7212c`](https://github.com/area17/twill/commit/14f7212c)
- Table aliases in Eloquent scopes [`1d98f3b8`](https://github.com/area17/twill/commit/1d98f3b8) [`c37d5654`](https://github.com/area17/twill/commit/c37d5654)
- Remove permalink field in modal if module has no slugs [`cfaabe47`](https://github.com/area17/twill/commit/cfaabe47) [`c0649443`](https://github.com/area17/twill/commit/c0649443) [`7bcd3d85`](https://github.com/area17/twill/commit/7bcd3d85)
- Tests suite for browsers [`7c0bad69`](https://github.com/area17/twill/commit/7c0bad69) [`d529cb48`](https://github.com/area17/twill/commit/d529cb48) [`2a862e0c`](https://github.com/area17/twill/commit/2a862e0c) [`01fb29e0`](https://github.com/area17/twill/commit/01fb29e0) [`f15034fa`](https://github.com/area17/twill/commit/f15034fa) [`99dde9c4`](https://github.com/area17/twill/commit/99dde9c4) [`3d45a04b`](https://github.com/area17/twill/commit/3d45a04b) [`2b6cefcd`](https://github.com/area17/twill/commit/2b6cefcd) [`d7e83e60`](https://github.com/area17/twill/commit/d7e83e60) [`54517646`](https://github.com/area17/twill/commit/54517646) [`8aa2ca60`](https://github.com/area17/twill/commit/8aa2ca60) [`9c408428`](https://github.com/area17/twill/commit/9c408428) [`deeaf4e3`](https://github.com/area17/twill/commit/deeaf4e3) [`9ee6a91f`](https://github.com/area17/twill/commit/9ee6a91f)
- Complete German translations [`7c6c94e2`](https://github.com/area17/twill/commit/7c6c94e2) [`66126b45`](https://github.com/area17/twill/commit/66126b45) [`279e0b5e`](https://github.com/area17/twill/commit/279e0b5e) [`8e982618`](https://github.com/area17/twill/commit/8e982618) [`8de83a16`](https://github.com/area17/twill/commit/8de83a16) [`a26ac6d4`](https://github.com/area17/twill/commit/a26ac6d4) [`bd69cac5`](https://github.com/area17/twill/commit/bd69cac5) [`93e347d9`](https://github.com/area17/twill/commit/93e347d9) [`65bb2d3c`](https://github.com/area17/twill/commit/65bb2d3c) [`d8b7fc93`](https://github.com/area17/twill/commit/d8b7fc93) [`1187482a`](https://github.com/area17/twill/commit/1187482a)
- Make bulk-publish notice translatable [`585c1e3d`](https://github.com/area17/twill/commit/585c1e3d)

### Chores

- Add and improve existing DocBlocks [`17390bb6`](https://github.com/area17/twill/commit/17390bb6f5c5f14f92d97ec5a7881f5e5dq382a45) [`55027c07`](https://github.com/area17/twill/commit/55027c07473168bc7c70e9bcad019521fd148d62) [`579cc87e`](https://github.com/area17/twill/commit/579cc87edc6ac7c9c259df9f1f66c5a86de9f897) [`978ba592`](https://github.com/area17/twill/commit/978ba592bfb9e6a738cce921e320f3c4bcce3fc8) [`5770a424`](https://github.com/area17/twill/commit/5770a424d8715ea5772e93a609349b9461b9bd0a) [`55c29db8`](https://github.com/area17/twill/commit/55c29db85a3a29f6cb05e39ac4dd845fe9e9fe0b) [`aa072490`](https://github.com/area17/twill/commit/aa0724903c88157326c5630e7597c45653deddbe) [`fb0ef006`](https://github.com/area17/twill/commit/fb0ef006155941a1ed8921d5d03b4f1da5232afe) [`f12bc787`](https://github.com/area17/twill/commit/f12bc787bb794aba6ad0946c3bc723f8b5f3c5ba) [`7f24d980`](https://github.com/area17/twill/commit/7f24d980e4b30c9e01bdb60f9315daf1e655d8b3) [`e7409b8d`](https://github.com/area17/twill/commit/e7409b8dd62194a224bcb281708011eb0e05feb6) [`aa80d1ad`](https://github.com/area17/twill/commit/aa80d1ad272de0560308b28f83cbabeda00f1062) [`2e2d5342`](https://github.com/area17/twill/commit/2e2d534250cb72a3819e636b5160670c55b17bd1) [`e2b9f027`](https://github.com/area17/twill/commit/e2b9f02711fc097ec74bcf8b33c87829c5f21625) [`a2a43657`](https://github.com/area17/twill/commit/a2a43657d81b42b0821946ef6b8e027c3564fdd5) [`4151d123`](https://github.com/area17/twill/commit/4151d123c9fc42ac66f185b224debf4263bcdfa0) [`3adddbed`](https://github.com/area17/twill/commit/3adddbedf35cae8f11ba69275d08ba5d1c9d4ead) [`36a695ce`](https://github.com/area17/twill/commit/36a695ce3af35fe486be0b03b5007d2a2f0b7520) [`3bafed88`](https://github.com/area17/twill/commit/3bafed88527324b49b7dac92f9331204a9fc54a7) [`28c6e23e`](https://github.com/area17/twill/commit/28c6e23e51b4c0aaa1451d63e0145e379d551a8d) [`371fb3bd`](https://github.com/area17/twill/commit/371fb3bd11f2c96d8008e7638ef845e029a9957e) [`2f1767ed`](https://github.com/area17/twill/commit/2f1767ed87a1ec2e54b617126d307cad2760b2e0) [`e8609825`](https://github.com/area17/twill/commit/e86098256e2174d71553d07b2fb24f87b3d03058) [`1664edb8`](https://github.com/area17/twill/commit/1664edb8787a1f1de25fcb656d20808e03e468ae)
- Fix npm dependencies security vulnerabilities [`39fb3471`](https://github.com/area17/twill/commit/39fb34712fb34624271b12dd79da5793a5968a12) [`ef9dd22a`](https://github.com/area17/twill/commit/ef9dd22a893b7065afd79590587a2af2a4d66a5e) [`304e16b1`](https://github.com/area17/twill/commit/304e16b1b702b99bbf57422cdf825e0f97089963) [`8ea82edd`](https://github.com/area17/twill/commit/8ea82edd) [`f28f8e68`](https://github.com/area17/twill/commit/f28f8e68) [`b42ef685`](https://github.com/area17/twill/commit/b42ef685)
- Allow Google2FA-QRCode 2.0 to add support for chillerlan php-qrcode [`30f296a8`](https://github.com/area17/twill/commit/30f296a8) [`53067b1a`](https://github.com/area17/twill/commit/53067b1a) [`6e19e7bb`](https://github.com/area17/twill/commit/6e19e7bb) [`ee61209d`](https://github.com/area17/twill/commit/ee61209d) [`b7a892d9`](https://github.com/area17/twill/commit/b7a892d9) [`5b3640a5`](https://github.com/area17/twill/commit/5b3640a5) [`eed1476b`](https://github.com/area17/twill/commit/eed1476b) [`174a684e`](https://github.com/area17/twill/commit/174a684e) [`#1074`](https://github.com/area17/twill/pull/1074)

## 2.4.0 (2021-06-15)

### Added

- Add support for browsers using HasRelated behavior [`580faa47`](https://github.com/area17/twill/commit/580faa474057a816dfaf7d873b3eba6c728872c0)
- Allow `dev_mode` to be set in `.env` [`95874a1a`](https://github.com/area17/twill/commit/95874a1a8c7169b7062647b41b0391c8ce8c09f0)
- Add custom subject to notifications [`f137b213`](https://github.com/area17/twill/commit/f137b2133aaa2b8e8dee3c68a440b444c959efcf)

### Fixed

- 🚨 Fix logout security vulnerability [`e84abd4f`](https://github.com/area17/twill/commit/e84abd4fbbe27f08bf94e020dd27bdba622014da)
- Update capsule provider booting and registration (fixing view:cache) [`5a086a32`](https://github.com/area17/twill/commit/5a086a32d04e3889731ce547aa6b673e883ab1ce)
- Save wysiwyg sourcecode value into store on change [`83d3eb62`](https://github.com/area17/twill/commit/83d3eb621984a5b26e165d044d53cbcb094999f6) [`8d9a3d21`](https://github.com/area17/twill/commit/8d9a3d21422271621807d6073bdbfdb41fc1277e) [`c4911046`](https://github.com/area17/twill/commit/c4911046d1d8964e83176005fa9ec1bee33c512c) [`4aebb3e3`](https://github.com/area17/twill/commit/4aebb3e3a76c09613af3ee660585a63bbff45914)

### Improved

- Move some hard coded texts to lang file [`20534801`](https://github.com/area17/twill/commit/205348017eb516effade55aaabcf9fd3650f8821)
- Adds docs with examples for conditional fields [`33717de7`](https://github.com/area17/twill/commit/33717de7927203a6ab4af9d5e7464da99591f457) [`908fb63f`](https://github.com/area17/twill/commit/908fb63fb850aa51e52c9e8a530010192298a7c4) [`05aef80c`](https://github.com/area17/twill/commit/05aef80c11cca915954c9d3d908352476912915b)
- Fix typo in multi select inline example [`8abefe1c`](https://github.com/area17/twill/commit/8abefe1c372543d641b6fb63aeac1a8122c428b9)

## 2.3.1 (2021-06-05)

### Fixed

- Media library upload regressions (hotfix) [#959](https://github.com/area17/twill/pull/959)

## 2.3.0 (2021-06-03)

### Added

- Configuration system for capsules [#924](https://github.com/area17/twill/pull/924)
- Automated `belongsTo` browser field relationship [#913](https://github.com/area17/twill/pull/913)
- Internal API documentation [#929](https://github.com/area17/twill/pull/929)
- Connected fields array values support [#935](https://github.com/area17/twill/pull/935)
- Capsules finder methods and helpers [`d30f341d`](https://github.com/area17/twill/commit/d30f341d4fde21bbb696ff6fb7a863041c1f4409)
- Ability to retrieve all image crops in a single call, as generated URLs or data arrays [#928](https://github.com/area17/twill/pull/928)
- Ability to disable crops entirely on the `medias` field [#922](https://github.com/area17/twill/pull/922) [`0a153c29`](https://github.com/area17/twill/commit/0a153c29a99108823770402521771d0954ce784e) [`7dbfaef6`](https://github.com/area17/twill/commit/7dbfaef6e0641bf938a1b4676bea3330f862cc21)
- Support for custom title key in related browsers [#942](https://github.com/area17/twill/pull/942)

### Fixed

- Custom create modal view regression [#920](https://github.com/area17/twill/pull/920)
- Subdomain routing regression [#908](https://github.com/area17/twill/pull/908)
- Media Library uploads regression when using Postgres [#941](https://github.com/area17/twill/pull/941)
- Fix "Unresolvable dependency resolving" problem [#952](https://github.com/area17/twill/pull/952)
- Ensures app/Twill/Capsules exists during install [`deff0a87`](https://github.com/area17/twill/commit/deff0a87c7612e1c745da993b0f2286f08ee5d55)
- Fix minor typos in documentation [`3dcf4fa2`](https://github.com/area17/twill/commit/3dcf4fa270ef0e58d6b81399df37bd2dcac3d51a)

### Improved

- Docs: update form fields documentation [#945](https://github.com/area17/twill/pull/945)
- Docs: add missing docs for $permalinkBase [`1609765b`](https://github.com/area17/twill/commit/1609765b5d47131c9830a01731d13fd495cdf1ce)
- Docs: add duplicate param to $indexOptions [`d665967d`](https://github.com/area17/twill/commit/d665967d9ae7deb9c6d37f55a6fdde45a8455bec)
- Docs: add skipCreateModal option to controller docs [`08fd0167`](https://github.com/area17/twill/commit/08fd01672b6e915e7ee6016edae0c852d3f0ae80)


### Chores

- Update rebase GitHub Actions version to support forks with branches of the same name [`e08ee215`](https://github.com/area17/twill/commit/e08ee215859f28d989792765bc46ccb5f20afeb9)


## 2.2.1 (2021-05-18)

### Fixed

- Capsules without translations: move HasCapsules trait to the base model [`8b730e81`](https://github.com/area17/twill/commit/8b730e81492ecf61f21c835baa87feae87a37bb9)
- Laravel configuration caching: set the capsules list config as an array [`2b5b07a5`](https://github.com/area17/twill/commit/2b5b07a58a77193138c147393e95516df0dd46b3)

### Improved

- Update documentation to reflect Laravel 8 support [`c01c426d`](https://github.com/area17/twill/commit/c01c426d19c63f900d5bdd18f263fcee8fd65958)
- Fix wording and add proper method description [`e1293551`](https://github.com/area17/twill/commit/e1293551239d1283a7cb1c798444083b90dd6ce0)

## 2.2.0 (2021-05-14)

### Added

- **Laravel 8 and PHP 8 support** [#740](https://github.com/area17/twill/pull/740)
  - After long months of compatibility testing and stability testing in production, Twill is finally compatible with the latest versions of PHP and the Laravel framework.
- **Twill Capsules** [#729](https://github.com/area17/twill/pull/729)
  - Twill Capsules are modules encapsulated in a single folder. Like modules, they can be generated from the command line, but they can also be installed from a remote repository. We think this will enable new ways for the community and ourselves to share reusable and customizable Twill modules.
- **Deep nested modules** [#775](https://github.com/area17/twill/pull/775)
  - When using dot notation to nest modules, it was previously only possible to use a single children module. With those changes, it is now possible to go add deep as needed.
- **Image and file replacement from the media library** [#660](https://github.com/area17/twill/pull/660)
  - This new feature enables your users to replace an image or file globally from the media library, preserving all relationships with records, blocks and repeaters where the image or file has been attached.
- Nested repeaters support outside of blocks [`ce75aff1`](https://github.com/area17/twill/commit/ce75aff1c21f910b79bd0e514e0dcf6898561fe9)
- Algolia search in docs [`c1d85e48`](https://github.com/area17/twill/commit/c1d85e48a680dde0a23d65038fdb9687e3e4a44b)
- Glide cloud sources classes to allow Glide and S3 integration [`11f0f502`](https://github.com/area17/twill/commit/11f0f5027f3657f068004066a301a5aa035de8ea)
- Allow renaming tag tables [`3f34da73`](https://github.com/area17/twill/commit/3f34da73310e6301277a3950cfcbcbdb2113522e)
- Allow passing email and password directly on the command line when creating a superadmin user [`812331d1`](https://github.com/area17/twill/commit/812331d12ea58763ecbcd2c2b16ca998c2b1e66c)
- Add target attribute in Twill navigation entries to allow opening in new window [`3418edcc`](https://github.com/area17/twill/commit/3418edccc01a7636204caf487ed41e5e7d4e3299)
- Automate CMS permalink for nested modules [`94742f29`](https://github.com/area17/twill/commit/94742f29651509b3a8e8a621d9376bb81515c9a5)
- Add ability to store more data from the Google Maps API [#721](https://github.com/area17/twill/pull/721) [`dfaa5f8e`](https://github.com/area17/twill/commit/dfaa5f8e36c928e5e20f677765d7c33243d181b6)
- Allow to use a different name than relationship when hydrating a repeater [#717](https://github.com/area17/twill/pull/717) [`9ead6d5c`](https://github.com/area17/twill/commit/9ead6d5c588243f2727906799e1d36c22d5c7c3c)
- Support custom model identifier key in controller [#780](https://github.com/area17/twill/pull/780) [`fce6465d`](https://github.com/area17/twill/commit/fce6465d0a5ed51a73400092a6a91d8f7f2a3ed6)
- Add optional note into the Browser modal (via the Browser Field) [`b4cb5b83`](https://github.com/area17/twill/commit/b4cb5b830e5cda6b4ec1783bfd3c49eaed1afc62)

### Fixed

- **2.1 regressions with blocks configuration** [#813](https://github.com/area17/twill/pull/813)
  - Block name (configuration key) and component not matching issues
  - Duplicate blocks and Twill blocks conflicts issues
  - Show a better message when block is missing [#712](https://github.com/area17/twill/pull/712) [`3dd66067`](https://github.com/area17/twill/commit/3dd660677820388c4c63cb6d8d17f81cc3831c73)
  - Add default icon and trigger (repeater) to Block [`ae162ac3`](https://github.com/area17/twill/commit/ae162ac3a34e83641f8dc5d56d960edfb6bfacb6)
  - Logging deprecated use of block and repeater definitions in config [`1beadaae`](https://github.com/area17/twill/commit/1beadaae03426eccf4a3ed7432a498b99da8bad5)
  - Generate Vue Blocks only for 'compiled' and confirm overwriting if file exists [`bc7dfa50`](https://github.com/area17/twill/commit/bc7dfa505a9fdd92d6aefe3c34ed536c6cb679a7)
- **Fix uploads with custom root path on S3** [`a92b6232`](https://github.com/area17/twill/commit/a92b62322aa5f58f02635dbdd85ae66a587230c8)
  - Closes [#553](https://github.com/area17/twill/pull/553). The rationale for keeping the S3_ROOT in the uuid in database is to allow changing its value while keeping, for example, older uploads in the previous root (which might be connected to a different Imgix source). It would also be a breaking change to remove the root from the uuid in database. We might reconsider this for Twill 3.
- **Fix S3-compatible storage compatibility** (DO Spaces) [`4c8cc698`](https://github.com/area17/twill/commit/4c8cc698389cb90c0fc460822c36b72a2490c65)
- "Create and add another" fix for macOS Safari/FF [#782](https://github.com/area17/twill/pull/782) [`2d403516`](https://github.com/area17/twill/commit/2d403516e6d81289d9a61f35e20f297121cb550c)
- Nested listing: maxDepth now respected [`9e9c7f8d`](https://github.com/area17/twill/commit/9e9c7f8d1f9931fc47451576d921859c289976a1)
- Publication bug in module create modal [#732](https://github.com/area17/twill/pull/732) [`7afdca7f`](https://github.com/area17/twill/commit/7afdca7fb5d1672e96452848eca52abe9f5b3521)
- Broken variables for FR emails [#734](https://github.com/area17/twill/pull/734) [`2cc9984a`](https://github.com/area17/twill/commit/2cc9984a77a6d69118a1a01fce5df39534afa661)
- Drop index error on mediables table [#800](https://github.com/area17/twill/pull/800) [`a29a0294`](https://github.com/area17/twill/commit/a29a029471a0eb4dd063da98548d53b09a1cb214)
- Artisan make:module (deprecated) should prompt for input [#725](https://github.com/area17/twill/pull/725) [`a5b3ffc2`](https://github.com/area17/twill/commit/a5b3ffc20ab7826bff6825f28982444e7272537d)
- Fix for checkboxes stored as string [#694](https://github.com/area17/twill/pull/694) [`27c9887c`](https://github.com/area17/twill/commit/27c9887cd07773827a617a50e37de947d7476048) [`736a0642`](https://github.com/area17/twill/commit/736a064262bafd4c47b0562136b8a2d859c00ca0)
- Prevent the permalink preview from wrapping when over 52 characters long [`33b5641d`](https://github.com/area17/twill/commit/33b5641d4f423350f350bc783e0f2a678ca652ad)
- Don't create superuser while on no-interaction [`efac9ff7`](https://github.com/area17/twill/commit/efac9ff7ea0d9876cae2c93c284b0f3add37780e)
- Route name duplicating prefix and module [`f446363b`](https://github.com/area17/twill/commit/f446363b6aedb4a7d266faac048ba10cc61ac0fb)
- Don't render languages columns when only one is available [`c46bdcda`](https://github.com/area17/twill/commit/c46bdcda9703e31a7596aac09883568432fd1e9b)
- Fixed nested listing on undefined children [`ef9a8dfb`](https://github.com/area17/twill/commit/ef9a8dfbe1a8933da36e5ebbee2a43223ccd8048)

### Improved

- Documentation
  - **Updated block editor documentation** [`9df76dd4`](https://github.com/area17/twill/commit/9df76dd4bd674e6c9a0b7eb974fef51f7604d0b7)
  - Documentation for custom vue component [`da6557f2`](https://github.com/area17/twill/commit/da6557f29333b0974079b79c5872dd19353574c6)
  - Documentation with some examples for the MultiBrowser feature [`66c12808`](https://github.com/area17/twill/commit/66c12808c024c3f2037ad425965b08bc26932f96) [`36cb3f48`](https://github.com/area17/twill/commit/36cb3f48562686f21d0879276e2a431b31155c32)
  - Added some missing optional $cropName and $media params for various image methods [#762](https://github.com/area17/twill/pull/762) [`8188f864`](https://github.com/area17/twill/commit/8188f8643ba768da8ff4c7def3e8ee2e0ba04e43)
- Vapor support
  - We've had the opportunity to deploy Twill instances to Laravel Vapor and refactored the way Twill deals with its own assets urls internally in the process.
  - Use vendor Twill manifest when public/ is missing [`5ff29afe`](https://github.com/area17/twill/commit/5ff29afee21d2a46a8112b30b746ea4855c2651e)
- i18n
  - Add czech translations [`71c976dd`](https://github.com/area17/twill/commit/71c976ddbdb35e4a33f894d0cdbc6af3d43b0ebf)
  - Add Turkish localization support for flatpickr and vue-timeago packages [`d725a7d4`](https://github.com/area17/twill/commit/d725a7d4df0dc8ad3f23bc4f98329a1e95906df4)
  - fixed a typo in the German language [`29eb3a9c`](https://github.com/area17/twill/commit/29eb3a9cf36a03b3b555c0b6dded0cbac341b1e5)
  - Make parts of settings & dashboard translatable [`843c1da2`](https://github.com/area17/twill/commit/843c1da2f8bb587b31811dcffa3fa673e798fcb7)
  - Russian language update [`f87d99f2`](https://github.com/area17/twill/commit/f87d99f24d7150eabdc83d970201811eb29fc7d7)
  - Add some missing Polish language strings [`30fe3593`](https://github.com/area17/twill/commit/30fe3593839376623919f90db204283e1b0ed7e8)
- Misc.
  - Wysiwyg with Quill : set default styling for italic and underline in the editor [`32bbfb4f`](https://github.com/area17/twill/commit/32bbfb4ff371b820bb2944469932eafbf98aaffa)
  - Consider morphed models when getting the name of the class to obtain the repository [`cad35392`](https://github.com/area17/twill/commit/cad35392ce4d747553aa8ce307497268cd4278cd)
  - Easier to use starter route [`0d9c0d09`](https://github.com/area17/twill/commit/0d9c0d094ab8256498dd7301d24d5f99d1c6d495)
  - Make slug model namespace dynamic [`308f2a92`](https://github.com/area17/twill/commit/308f2a92735b4d2ef77cf21069120f79e119b93f) [`3ba04e51`](https://github.com/area17/twill/commit/3ba04e51e2c66f7a54e21b2450185ee89bd35dfb) [`7cbbc253`](https://github.com/area17/twill/commit/7cbbc2537c50a02caedcc91305626392b5ad6b7c)
  - Preserve the blocks order when c
  alling a block editor [`deafbef0`](https://github.com/area17/twill/commit/deafbef0bf531db42749e8132f2d39bdc3d68efc)
  - Add fallback for adminEditUrl in browser fields [`a55f9848`](https://github.com/area17/twill/commit/a55f98484f10d1c9244c555270ab298e3c79cb88) [`3b16f22d`](https://github.com/area17/twill/commit/3b16f22d5bef3ad0720eb848ee2f14cd468b3614)

### Chores

- CI and testing
  - **Setup CI tests on GitHub Actions** [`03ab2714`](https://github.com/area17/twill/commit/03ab27141d6505d3e88eb65dd24c6b9e87c6f8ce)
    - With Travis CI dropping free support for open source projects, we've had to migrate to a new CI service. GitHub Actions made the most sense for us. The migration took time to get right but we now test a larger compatibility matrix, from PHP 7.1 to PHP 8 and from Laravel 5.8 to 8.
  - Add on demand rebase GitHub Actions workflow [`d2a5c8e3`](https://github.com/area17/twill/commit/d2a5c8e3566368eef3318dbec09fd5e26e1a5471)
  - Add on demand frontend build GitHub Actions workflow [`c8bdf928`](https://github.com/area17/twill/commit/c8bdf9281d47b6d905fcee6108cc0c10aa0bee79)
  - Turn on TestBench debug [`8c40b7d9`](https://github.com/area17/twill/commit/8c40b7d90accf608c888c7e481d42932afcb5c7a)
  - Display response errors [`7bb74eec`](https://github.com/area17/twill/commit/7bb74eec6eb6af899ccd1d3cb9bb834a3250d4b8)
- Community updates
  - **Add Contributor Covenant** [`c16572d7`](https://github.com/area17/twill/commit/c16572d7afc3c5593811f7caa8d6d667e9a5a66e)
  - **Add GitHub templates (issues and PRs)** [`864d7cf4`](https://github.com/area17/twill/commit/864d7cf4947e896d6492fa3e48a438f9fc461075)
- Security
  - Fix npm dependencies vulnerabilities
  - Fix npm dependencies vulnerabilities in docs

## 2.1.1 (2020-07-20)

### Fixed

- Fix block editor group parameter update [`121b0166`](https://github.com/area17/twill/commit/121b016653dda342ea36160c9b9a42290000ecc1)
  - Both `group` and `groups` can be used and receive a string or an array
- Fix #701: settings forms regression [`053c4ea4`](https://github.com/area17/twill/commit/053c4ea45cbdcc1534fae90972832f519366dc27)
- Fix skipCreateModal option on submodules [`2474ae56`](https://github.com/area17/twill/commit/2474ae56f55fd50145e489570b5f20656d472d6c)
- Fix #670: multi select field doesn't prevent user from picking duplicated option [`33e54a43`](https://github.com/area17/twill/commit/33e54a43e8d5fe4be53abe8ec614b29eb3aee691)
- Remove legacy product requirement on required fields (#697) (#699) [`89469159`](https://github.com/area17/twill/commit/894691590fc0fe2260ce6aa47387e2021b6fe4a7)
- Fix regression introduced by #620 (#702) [`52b66289`](https://github.com/area17/twill/commit/52b66289e4f7590a2d0d73baa38ab5f63596c923)

## 2.1.0 (2020-07-15)

### Added
- **Self-contained blocks** (#597) [`1e95b0ac`](https://github.com/area17/twill/commit/1e95b0ac) [`9ae502a4`](https://github.com/area17/twill/commit/9ae502a46ed31f82ebda5426dbff5f56228e2d25) [`948985ef`](https://github.com/area17/twill/commit/948985ef22d70a08287ccdf7583ca802f6182833) [`66fa7c5e`](https://github.com/area17/twill/commit/66fa7c5e16c0ce34f0edc1aab74a52ef0d232401) [`c00759ee`](https://github.com/area17/twill/commit/c00759ee7cad76aea16ec7f04c693ba1cd046b9d)
  - This change allows defining blocks without adding them to the `twill.block_editor` configuration
  - This is backwards compatible with blocks already defined in configuration
  - Repeaters are now created in a dedicated folder: `views/admin/repeaters` by default, but your existing repeaters in the `views/admin/blocks` file will still work
  - Annotations are now supported in blocks and repeaters Blade files:
    - Provide a title with `@twillPropTitle` or `@twillBlockTitle` or `@twillRepeaterTitle`
    - Provide an icon with `@twillPropIcon` or `@twillBlockIcon` or `@twillRepeaterIcon`
    - Provide a group with `@twillPropGroup` or `@twillBlockGroup` or `@twillRepeaterGroup` (defaults to `app`)
    - Provide a repeater trigger label with `@twillPropTrigger` or `@twillRepeaterTrigger`
    - Provide a repeater max items with `@twillPropMax` or `@twillRepeaterMax`
    - Define a block or repeater as compiled with `@twillPropCompiled` or `@twillBlockCompiled` or `@twillRepeaterCompiled`
    - Define a block or repeater component with `@twillPropComponent` or `@twillBlockComponent` or `@twillRepeaterComponent`
    - Example:
      ```php
      @twillBlockTitle('Body text')
      @twillBlockIcon('text')

      @formField('wysiwyg', [
          'name' => 'text',
          'label' => 'Text',
      ])
      ```
    - This change also provides new Artisan commands:
      - `php artisan twill:make:block {name} {baseBlock} {icon}`, which generates a new block based on a provided block
      - `php artisan twill:list:blocks`, which lists blocks with a couple of options:
        - `-s|--shorter` for a shorter table,
        - `-b|--blocks` for blocks only,
        - `-r|--repeaters` for repeaters only,
        - `-a|--app` for app blocks/repeaters only,
        - `-c|--custom` for app blocks/repeaters overriding Twill blocks/repeaters only,
        - `-t|--twill` for Twill blocks/repeaters only
      - `php artisan twill:list:icons`, which lists all icons available
      - `php artisan twill:make:module`, equivalent to the now deprecated `twill:module` which will be removed in Twill 3.0.
- **Resolve npm modules from root app** (#617) [`360d82c1`](https://github.com/area17/twill/commit/360d82c13daed5a921d5c324d3af7dd2a6945531)
  - This change allows requiring node modules from the root project folder. Previously, custom Vue components could only use npm packages installed by Twill itself. With this change, any npm package from the main app can be required. We do this by adding the root `npm_modules` folder to webpack’s module resolver.
- **Add a new option to skip the add new modal to create records** (#642) [`1ec1f428`](https://github.com/area17/twill/commit/1ec1f428f47b8fee89b0606d6b644041b9fc35d6)
  - This change allows users to create full records by landing directly on the form when adding new records
  - It is enabled through the new `skipCreateModal` option of a module's controller `$indexOptions` array.
- **Implement a new behavior to allow saving repeaters into json columns** (#654) [`85f96306`](https://github.com/area17/twill/commit/85f963069fdfa5dae19a2e5a10e7af5817686cd0) [`a2fffa4f`](https://github.com/area17/twill/commit/a2fffa4fba1ec25a99df075a87e30a71bcab5f6d)
  - This trait is not intended to replace main repeaters but to give a quick and easy alternative for simple elements where creating a new table might be an overkill.
  - Simply define an array with repeater names on your repository: `protected $jsonRepeaters = [ 'REPEATER_NAME_1', 'REPEATER_NAME_2', ... ]`
- **Add new options to medias and files form fields** [`c564ecc2`](https://github.com/area17/twill/commit/c564ecc23f9191272b72e768e4f50d0aa49bf955) [`571cc1e9`](https://github.com/area17/twill/commit/571cc1e9f96a388e6b6a2e089346469aba2ee261)
  - filesizeMax, on the files field, to prevent selecting a file which filesize is above provided value in mb
  - widthMin, on the medias field, to prevent selecting an image which width is below provided value in px
  - heightMin, on the medias field, to prevent selecting an image which height is below provided value in px
- Added new option to display filenames of images in the media library grid (#658) [`2034b6e7`](https://github.com/area17/twill/commit/2034b6e7d37c5a6803d3e2ee79d6e53cbdfa115c)
- Add confirmation modal option to checkbox and radio form fields (#687) [`41261c18`](https://github.com/area17/twill/commit/41261c18c2e91d5b362cc4029f7aa020693eb962)  [`b152cdd9`](https://github.com/area17/twill/commit/b152cdd90590d0f8ba3bf5d9b49e8c03001accdc) [`fe6ec3d0`](https://github.com/area17/twill/commit/fe6ec3d0bc3b52064324d5a0ea92f310064f67f8)
- Allow user to filter by unused images or files in the media library (#688) [`a52349a9`](https://github.com/area17/twill/commit/a52349a9ed2632fe1f43e6b640c4c867314c24e2) [`261941fc`](https://github.com/area17/twill/commit/261941fc143e4380c46fa0c976bc271bafb7ad97)
- Add admin title tag suffix to config (#680) [`3aefcdc3`](https://github.com/area17/twill/commit/3aefcdc3101f9f7bdec51fc61186bed48f4cba1a)
- Support checkbox form field in settings [`d62d303f`](https://github.com/area17/twill/commit/d62d303fba5e3ea0e6bcd8e838933bdab0c270cc)
- Support date_picker and color form fields in settings (#576) [`f66aaa68`](https://github.com/area17/twill/commit/f66aaa6821debdeeb3c7b24633d65d66d444a3d4)
- Allow 3 columns layout by setting up a middle columns (#638) [`2b2e3e49`](https://github.com/area17/twill/commit/2b2e3e49f4c1e73a96aaa6ce7ffcaa20c084cbd0)
- Allow browsers to sync extra pivot attributes (#629) [`f33b8825`](https://github.com/area17/twill/commit/f33b88254ef81336316b26aa744e7348d2bfa36e)
- Provide env variable for configuring custom s3 hosts [`5894ccce`](https://github.com/area17/twill/commit/5894ccce4c4a8dd0e60b3583c6fa3069d1119640)
- Add ability to provide a custom morphed repeaters name (#679) [`3a118bc3`](https://github.com/area17/twill/commit/3a118bc375a4bbb0045fe553822023a80052c515)
- Support required option on wysiwyg form field [`e780bcfd`](https://github.com/area17/twill/commit/e780bcfd)
- Support disabled option on select form field [`e780bcfd`](https://github.com/area17/twill/commit/e780bcfd)
- Allow format change in datepicker field (#628) [`936057d8`](https://github.com/area17/twill/commit/936057d8cda799410918813db3d671b97795c04f)
- Allow datepicker format in publication dates (#636) [`477bc288`](https://github.com/area17/twill/commit/477bc288dd9689ada673b5790248789a803157e9)
- Allow a model class to be passed instead of the relation name (#640/#619) [`8bf8e8f0`](https://github.com/area17/twill/commit/8bf8e8f099317da7ddebb30300a6d84f90bc8d47)
- Add new `buttonOnTop` option to medias, files and browser form fields (#598) [`964c99a0`](https://github.com/area17/twill/commit/964c99a0b7e2ab0c547198cabc436151b9a8000b) [`cf8ead0d`](https://github.com/area17/twill/commit/cf8ead0ddfd631612f6d40efd355fe41cf5e0dbd)
- Provide optional parameters for changing label and including a note in tags form field [`0d535710`](https://github.com/area17/twill/commit/0d5357103eb1209393b0e9f4748d02602cad231d)

### Fixed

- **Fix new listing actions behaviors** [`2d1b2eb5`](https://github.com/area17/twill/commit/2d1b2eb53cc11f3f5a41cf7335f2d53d416cb19e)
  - Destroy action was not removed when disabling forceDelete in indexOptions
  - Duplicate action was showing in trash and was not removed when disabled in indexOptions (which is by default)
  - Fix bulk destroy integration
- **Fix validation errors display** (#605) [`fc5b16a9`](https://github.com/area17/twill/commit/fc5b16a9179fcba57d3424ef2456a9efa74ce68f)
  - With the simplification of the exception handler in #561 the override of the invalidJson method was dropped by mistake. It is currently necessary as the frontend expects errors only in a validation exception response. In future improvements it would be better to keep the default Laravel response format and update the frontend to read one level deeper.
- **Fix local disk and Glide base url request scheme resolution** [`15a2dbea`](https://github.com/area17/twill/commit/15a2dbea5bbbfb3e77e23e821233d3b3e69d28bc)
  - [This commit](https://github.com/area17/twill/commit/876c93a22b660c14a52577019374a5cb3d569c77) introduced an issue by using request() in config. When using php artisan config:cache that request is not coming externally with the appropriate headers.
- **Fix draggable regression on datatable** [`a79e3d2e`](https://github.com/area17/twill/commit/a79e3d2ee95c4f8ef0e08bbc0f14ae8abad9c7c7)
- **Fix scheme being added twice when APP_URL has the scheme** (#651) [`2e5784cc`](https://github.com/area17/twill/commit/2e5784cc4f020607b76a97db5bc87b73915aad4d)
- Fix Twill form utils aliases on Laravel 7 [`c0018e5c`](https://github.com/area17/twill/commit/c0018e5c)
- Fix connected fields component alias [`4b61b78e`](https://github.com/area17/twill/commit/4b61b78e1ab746fbe9975be23993adfd4a6d1391)
- Fix typo in translation [`c0c492c5`](https://github.com/area17/twill/commit/c0c492c5805f2c72a8c89ac1ebfee0c97cfa087d)
- Fix child module redirect when adding new records in Laravel 6 / 7 [`e60c5066`](https://github.com/area17/twill/commit/e60c50668f548594d114b7cd438fba285853fb3e)
- Fix filesize limit uploader error display (#614) [`21263a3e`](https://github.com/area17/twill/commit/21263a3e2b67cc708ea8fa8ba4836db92dca7bdb)
- Fix missing row duplication event handler for nested tables (#615) [`c50b16d2`](https://github.com/area17/twill/commit/c50b16d267e47c66816c8233e26e8056375340d6)
- Fix translateTitle in a form (#648) [`8f5b0e28`](https://github.com/area17/twill/commit/8f5b0e287760bfc335ee8a091950178edfaa15e4)
- Fix route name duplicating prefix and module (#591) [`267eec02`](https://github.com/area17/twill/commit/267eec0205b3ae6d2209a1b689cd9434cbdf8bc4)
- Fix media form fields binding [`73104e62`](https://github.com/area17/twill/commit/73104e62)
- Fix blocks spacing [`56b5bc21`](https://github.com/area17/twill/commit/56b5bc21)
- Fix icons missing svg attributes [`c29b9dd6`](https://github.com/area17/twill/commit/c29b9dd6c57fc847955e8ae8fc197dae644d9af2)
- Fix tests [`cd50ea9d`](https://github.com/area17/twill/commit/cd50ea9d612a15a09fa336e08740ef1f5f6f40c6)
- Fix translated medias in settings (#620) [`e036313d`](https://github.com/area17/twill/commit/e036313d3bc10d9e44204561e6573b71ed8a418a)
- Fix select with no options (#625) [`3adea583`](https://github.com/area17/twill/commit/3adea583466de1f89f1aa9014c84419556cc7c78)
- Fix select field in settings: wrap string values in quotes (#653) [`86d72939`](https://github.com/area17/twill/commit/86d729391780c5ed942f83416845c1372faca3ee)
- Fix preview iframe resizing for blocks (#669) [`33f77ee7`](https://github.com/area17/twill/commit/33f77ee75deeb846f28a4c87fe16c70537135aee)
- Fix notifications url (#678) [`379df54c`](https://github.com/area17/twill/commit/379df54cb5ad6ac74e0b54e77999747ce7e8407d)
- Fix multi_select values escaping (#690) [`d54b94ac`](https://github.com/area17/twill/commit/d54b94ac470b81303afc8889edb8ba912ff86310)

### Improved

- **i18n improvements** (#624/#573/#632) [`fc856c40`](https://github.com/area17/twill/commit/fc856c40481bac97c58f27d2dd13942556d4b662) [`ba802587`](https://github.com/area17/twill/commit/ba802587c79104f177502083b51dd0ec50dbfa97)
  - Added a twill:sync-lang command to generate lang files from a CSV input
  - Configure vue-timeago to respect the set locale
  - Configure date-fns and flatpickr so they respect locale
  - Update french translations [`3b01dcfa`](https://github.com/area17/twill/commit/3b01dcfa4759d7083586454d910b67c4167bdf26) [`edd47e7a`](https://github.com/area17/twill/commit/edd47e7ad651a02cdd963232ea109eeb50eb31d3)
  - Add norwegian translations (#602) [`12acbd6f`](https://github.com/area17/twill/commit/12acbd6f965780d69c1e689a2bb67e496be11f0c)
  - Add italian translations [`6389ffdd`](https://github.com/area17/twill/commit/6389ffdde7ae742d13c37cd3770b88cb4b585da2) [`1155d33e`](https://github.com/area17/twill/commit/1155d33e0d71b15cff6be3242457c7fb6473be1c)
  - Add spanish translation (#689) [`65789733`](https://github.com/area17/twill/commit/65789733a7f9516f00660b9b264fdd2d1aa63f78) [`9163757d`](https://github.com/area17/twill/commit/9163757defeb63f47b9e0d3f3add1be03652134b)
  - Fixes in russian localization (#586) [`1d03572e`](https://github.com/area17/twill/commit/1d03572e59d68dd5819ef5d7baa2ea3a43c36420)
  - Dynamically set time format using Internationalization API [`8d53a489`](https://github.com/area17/twill/commit/8d53a4892c5d1668131e63c0e93a3e91e06850a9)
  - Update i18n keys and exported CSV [`4cef3f5d`](https://github.com/area17/twill/commit/4cef3f5d49afe595aaa5e85eabccbf0821108254) [`39346337`](https://github.com/area17/twill/commit/39346337d2ef79d86f914f6e8dc88138ced5a81e)
- **Log Twill APIs exceptions in browser console** [`c6956b8f`](https://github.com/area17/twill/commit/c6956b8f637af20f144f732b072e58baa12dd882)
- Improve tree reorder algorithm for nested modules (#600) [`c00bf011`](https://github.com/area17/twill/commit/c00bf011960474e1bdee4b1aca9fab59b71cbd16)
- Ignore symfony dump buttons in preview to be able to expand dd() output (#657) [`90524b47`](https://github.com/area17/twill/commit/90524b47d6c175ba9d04f63c7d3bf7796188df07)
- Pass the block currently being rendered to the layout view (#664) [`67df265a`](https://github.com/area17/twill/commit/67df265a5e860b26959e51a0bd44418274a64236)
- Make hydrateHandleBlocks and blocks preview recursive (#644) [`eb41a1b8`](https://github.com/area17/twill/commit/eb41a1b8a80b4bdfe5fe00d9db5e42fbe40c540e)
- Add secondary_navigation example in docs (#692) [`19879f01`](https://github.com/area17/twill/commit/19879f0133b082c204c8f5963369cef8f826e024)
- Documentation improvements [`ce7c8f95`](https://github.com/area17/twill/commit/ce7c8f9523e71a3ab9caca52d6a6b4580cd900fb) [`2c1a0d29`](https://github.com/area17/twill/commit/2c1a0d29e2fd42a7b41bacc75d2c38c546aea6c7) [`5e223e52`](https://github.com/area17/twill/commit/5e223e52dec7543442d5a3181952aca2dc5902b0) [`8822c21a`](https://github.com/area17/twill/commit/8822c21a836d20bcff28111e0c57165c8c798b86) [`af410508`](https://github.com/area17/twill/commit/af410508986539aabf4dafb290957d641281248a) [`0f7b3a05`](https://github.com/area17/twill/commit/0f7b3a0570a5ff19a1747fb8f070cc71fc9c56db) [`8aead1f3`](https://github.com/area17/twill/commit/8aead1f34e4e54204d8a793fc6cc256bfb7a7430) [`4a090409`](https://github.com/area17/twill/commit/4a0904091b9c4bfc71e954d9911ab3fb79f50b0f) [`e6c313c3`](https://github.com/area17/twill/commit/e6c313c3df50fab566746d507912294d71d19c05) [`3c2c3a1f`](https://github.com/area17/twill/commit/3c2c3a1f73f049958176f0f155a260b31bd3e242)

### Chore

- Update composer deps [`8082d7a2`](https://github.com/area17/twill/commit/8082d7a22bcc99628a22030ba1e5315499586d08)
- Update docs dependencies [`ff1c7c7a`](https://github.com/area17/twill/commit/ff1c7c7adfd6d5ce339cef42fb5ce5593f4cdc27)
- Update frontend build and version [`d49df23`](https://github.com/area17/twill/commit/d49df2394f40f1e0719398caa8d5c49bf75aed21)
- Update npm dependencies [`11711592`](https://github.com/area17/twill/commit/11711592ab8bd5ac8e377c236cba8c5618903f07)
- Add nested module test [`b2c2b01b`](https://github.com/area17/twill/commit/b2c2b01b5bff3d09afeef043f949724c9cb1fc41)
- Update distributed assets [`2a1fe7c5`](https://github.com/area17/twill/commit/2a1fe7c580ab4071a7afc0e9c81c642fea84f92e) [`a87aea14`](https://github.com/area17/twill/commit/a87aea14f03207b4226787d2ee1c47ced403bd38) [`8ca8cb23`](https://github.com/area17/twill/commit/8ca8cb239a8bdf9c7bcfde9ac4524c3355ce1d23) [`63a10782`](https://github.com/area17/twill/commit/63a10782871b4ec96f45dae45c84c364be93c115) [`5eb8654d`](https://github.com/area17/twill/commit/5eb8654d) [`2f48abc3`](https://github.com/area17/twill/commit/2f48abc37f2e78399561b505f72832b4f95ba37d) [`2b877921`](https://github.com/area17/twill/commit/2b8779212756ac963f34f4e42b0bf84e4b0a45e4) [`687ad5f6`](https://github.com/area17/twill/commit/687ad5f663d6f735aacf7daf97969b2ff593b230)


## 2.0.1 (2020-03-10)

### Fixed

- Fix fields not rendering after switching locales (#572) [`4ea1943b`](https://github.com/area17/twill/commit/4ea1943bb08b3ef627bfd982baf79eb8dc2af1a6)
- Fix duplicate action on Laravel 7 [`a30922b1`](https://github.com/area17/twill/commit/a30922b14d1ff27ea3497bb9b7da271f30f6147d)

### Improved

- Dutch language added [`32197d2c`](https://github.com/area17/twill/commit/32197d2c65dcc3f50debe458a7360d2a5804bac8)
- Update 2.0 docs [`9c56c171`](https://github.com/area17/twill/commit/9c56c1710d817d88cc30605e408876b00af68fbb)
- Add build status to README.md [`f6e7be9e`](https://github.com/area17/twill/commit/f6e7be9e0d8e58f13a0d3c518666279ec97cf833)

### Chore

- Update distributed assets [`4e19670c`](https://github.com/area17/twill/commit/4e19670cb62b9baeb37354c3998ce05add97328c)
- Update 2.0 changelog [`24fa0942`](https://github.com/area17/twill/commit/24fa0942a9185d575f603eb439630e766a7ac6d6)
- Update CHANGELOG.md [`cb923aa6`](https://github.com/area17/twill/commit/cb923aa6e6c046744e64bbe3f4ec1411b3c25198)
- Fix changelog release date [`57cd4e3e`](https://github.com/area17/twill/commit/57cd4e3e588d694ccb81b52afd5cb1e3b3a6c40c)


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
