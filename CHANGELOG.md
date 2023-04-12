# Changelog

All notable changes to `twill` will be documented in this file.

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

### Full changelog

* [3.x] #1401: Rename config files. by @haringsrob in https://github.com/area17/twill/pull/1434
* [3.x] Solve Prettier/ESLint conflict and review other formatting features by @ptrckvzn in https://github.com/area17/twill/pull/936
* [3.x] #1387: Rename namespaces. by @haringsrob in https://github.com/area17/twill/pull/1388
* [3.x] Setup psr2 + larastan by @haringsrob in https://github.com/area17/twill/pull/1413
* Feature/permissions management fix conflicts by @haringsrob in https://github.com/area17/twill/pull/1501
* [3.x] feat: Permissions Management by @pboivin in https://github.com/area17/twill/pull/1138
* [3.x] Blog example by @haringsrob in https://github.com/area17/twill/pull/1376
* [3.x] Coverage report generator. by @haringsrob in https://github.com/area17/twill/pull/1498
* [3.x] Update vueselect dependency to v3 by @mazeeblanke in https://github.com/area17/twill/pull/674
* [3.x] Better defaults by @haringsrob in https://github.com/area17/twill/pull/1484
* [3.x] #364: Min,max,step for numbers by @haringsrob in https://github.com/area17/twill/pull/1578
* [3.x] #94 trait order by @haringsrob in https://github.com/area17/twill/pull/1577
* [3.x] Update docs by @haringsrob in https://github.com/area17/twill/pull/1593
* [3.x] #1585 big int default by @haringsrob in https://github.com/area17/twill/pull/1600
* [3.x] #611: Document storage link. by @haringsrob in https://github.com/area17/twill/pull/1587
* [3.x] #378 tag long name by @haringsrob in https://github.com/area17/twill/pull/1580
* [3.x] #552 table columns freedom by @haringsrob in https://github.com/area17/twill/pull/1586
* [3.x] Flexible table columns by @haringsrob in https://github.com/area17/twill/pull/1632
* [3.x] Blade x components instead of custom directive by @haringsrob in https://github.com/area17/twill/pull/1360
* [3.x] allow super admins to reset 2fa for other stuck users by @YasienDwieb in https://github.com/area17/twill/pull/1419
* [3.x] Use default locale. by @haringsrob in https://github.com/area17/twill/pull/1525
* [3.x] #750 respect locale config by @haringsrob in https://github.com/area17/twill/pull/1594
* [3.x] #1626 document capsules by @haringsrob in https://github.com/area17/twill/pull/1628
* [3.x] Add selectable prop on VueSelect component by @kallefrombosnia in https://github.com/area17/twill/pull/1619
* [3.x] #436 allow empty allowed extensions by @haringsrob in https://github.com/area17/twill/pull/1584
* [3.x] Input masking by @haringsrob in https://github.com/area17/twill/pull/1605
* [3.x] #1502: Allow to use __( in twill properties. by @haringsrob in https://github.com/area17/twill/pull/1523
* [3.x] #1614 update rulesfortranslatedfields by @haringsrob in https://github.com/area17/twill/pull/1629
* [3.x] Nested block editor by @haringsrob in https://github.com/area17/twill/pull/1397
* [3.x] #1356: Add question to generate preview files for modules and blocks. by @haringsrob in https://github.com/area17/twill/pull/1377
* [3.x] Pivot repeaters dev by @haringsrob in https://github.com/area17/twill/pull/1550
* [3.x] #375 file delete folder by @haringsrob in https://github.com/area17/twill/pull/1579
* [3.x] Cleanups for alpha by @haringsrob in https://github.com/area17/twill/pull/1670
* [3.x] Code refactoring by @haringsrob in https://github.com/area17/twill/pull/1667
* [3.x] Limit revisions on model by @kallefrombosnia in https://github.com/area17/twill/pull/1479
* [3.x] Support morph browsers. by @haringsrob in https://github.com/area17/twill/pull/1528
* [3.x] Permissions fixes by @haringsrob in https://github.com/area17/twill/pull/1678
* Created TwillModel trait and TwillModelInterface by @OhKannaDuh in https://github.com/area17/twill/pull/1172
* [WIP] Tests by @haringsrob in https://github.com/area17/twill/pull/1711
* [3.x] OOP Twill filters by @haringsrob in https://github.com/area17/twill/pull/1686
* Fix function name in definition check by @galingong in https://github.com/area17/twill/pull/1717
* fix(blocks): Show 'Expand all' if block list is initially collapsed by @pboivin in https://github.com/area17/twill/pull/1727
* fix(repeaters): Expand new items when added or cloned by @pboivin in https://github.com/area17/twill/pull/1728
* [3.x] Add setters to the controller. by @haringsrob in https://github.com/area17/twill/pull/1716
* [3.x] Fix unpack by @haringsrob in https://github.com/area17/twill/pull/1735
* [3.x] #1703 check fillable by @haringsrob in https://github.com/area17/twill/pull/1736
* [3.x] Make dates timezone aware. by @haringsrob in https://github.com/area17/twill/pull/1724
* [3.x] #1704 fix vselect issues by @haringsrob in https://github.com/area17/twill/pull/1737
* [WIP] Pre release fixes by @haringsrob in https://github.com/area17/twill/pull/1739
* [2.x] Make 'Add Block' scrollable by @domihagen in https://github.com/area17/twill/pull/1464
* [2.x] feat(form): Move default submit options to controller method by @pboivin in https://github.com/area17/twill/pull/1719
* [2.x] feat(revisions): Allow draft revisions on top of published content by @pboivin in https://github.com/area17/twill/pull/1725
* [2.x] Improve twill build with custom icons. by @haringsrob in https://github.com/area17/twill/pull/1732
* [2.x] Fix clone js errors. by @haringsrob in https://github.com/area17/twill/pull/1734
* [2.x] Add fallback path for nested directory models. by @haringsrob in https://github.com/area17/twill/pull/1738
* ancestors sort by position desc to ensure the slug is following the tree by @thingasd in https://github.com/area17/twill/pull/1743
* Bump terser from 4.8.0 to 4.8.1 by @dependabot in https://github.com/area17/twill/pull/1747
* Bump terser from 4.8.0 to 4.8.1 in /docs by @dependabot in https://github.com/area17/twill/pull/1749
* [3.x] Compatibility/fix indexcolumns thumbnail by @haringsrob in https://github.com/area17/twill/pull/1780
* [3.x] Bugfix/create dialog languages by @haringsrob in https://github.com/area17/twill/pull/1782
* [3.x] Bugfix/block children relation ordering 3x by @haringsrob in https://github.com/area17/twill/pull/1785
* [3.x] Activities dashboard ignore inactive modules by @haringsrob in https://github.com/area17/twill/pull/1787
* [3.x] Swappable roles without composer edit. by @haringsrob in https://github.com/area17/twill/pull/1807
* [3.x] Nplus1issues by @haringsrob in https://github.com/area17/twill/pull/1793
* [3.x] #1778 subdomain routing issues by @haringsrob in https://github.com/area17/twill/pull/1786
* [3.x] Feature/firstbyfield create by @haringsrob in https://github.com/area17/twill/pull/1792
* [3.x] Fix custom build process by @haringsrob in https://github.com/area17/twill/pull/1809
* [3.x] Bugfix/block image rendering by @haringsrob in https://github.com/area17/twill/pull/1797
* [3.x] New Settings by @haringsrob in https://github.com/area17/twill/pull/1796
* [3.x] Bugfix custom icons by @haringsrob in https://github.com/area17/twill/pull/1824
* [3.x] various fixes by @haringsrob in https://github.com/area17/twill/pull/1826
* [3.x] duplicate by @haringsrob in https://github.com/area17/twill/pull/1823
* [3.x] User login disable by @haringsrob in https://github.com/area17/twill/pull/1828
* [3.x] Add link to edit method for table columns. by @haringsrob in https://github.com/area17/twill/pull/1834
* [3.x] Various updates for next alpha by @haringsrob in https://github.com/area17/twill/pull/1835
* [3.x] Rector upgrade compatibility + fix assets + compatibility fixes. by @haringsrob in https://github.com/area17/twill/pull/1842
* [3.x] fix disabled field in translated media field and slideshow by @haringsrob in https://github.com/area17/twill/pull/1847
* [3.x] 1767 by @haringsrob in https://github.com/area17/twill/pull/1851
* [3.x] 1754 by @haringsrob in https://github.com/area17/twill/pull/1852
* [3.x] #1790 by @haringsrob in https://github.com/area17/twill/pull/1853
* [3.x] 1848 by @haringsrob in https://github.com/area17/twill/pull/1854
* [3.x] Form builder singleton by @haringsrob in https://github.com/area17/twill/pull/1856
* [3.x] Fix twill navigation media overflow by @haringsrob in https://github.com/area17/twill/pull/1855
* [3.x] Nested modules improvements by @haringsrob in https://github.com/area17/twill/pull/1858
* [3.x] 1561 by @haringsrob in https://github.com/area17/twill/pull/1861
* [3.x] 1859 by @haringsrob in https://github.com/area17/twill/pull/1862
* [3.x] Guides & Documentation refresh by @haringsrob in https://github.com/area17/twill/pull/1874
* [3.x] fix: Hide nav item if user is not authorized (legacy config) by @pboivin in https://github.com/area17/twill/pull/1886
* [3.x] Fix console error on forms with permissions by @joyceverheije in https://github.com/area17/twill/pull/1890
* [3.x] Add link to users on index by @joyceverheije in https://github.com/area17/twill/pull/1891
* Fix reset password modal style when scrolled by @joyceverheije in https://github.com/area17/twill/pull/1894
* [3.x] Fix capsule navigation. by @haringsrob in https://github.com/area17/twill/pull/1901
* [3.x] TwillNavigation fluent + clear by @antonioribeiro in https://github.com/area17/twill/pull/1905
* [3.x] Fix aws adapter. by @haringsrob in https://github.com/area17/twill/pull/1902
* [3.x] Settings improvements by @haringsrob in https://github.com/area17/twill/pull/1910
* [3.x] Bugfix for nested modules. by @haringsrob in https://github.com/area17/twill/pull/1909
* Updating clone block icon in preview by @kylegoines in https://github.com/area17/twill/pull/1908
* [3.x] clone editor by @haringsrob in https://github.com/area17/twill/pull/1912
* [3.x] Checkbox note by @haringsrob in https://github.com/area17/twill/pull/1913
* [3.x] Various 2.x issues ported by @haringsrob in https://github.com/area17/twill/pull/1896
* [3.x] Add form builder for create forms. by @haringsrob in https://github.com/area17/twill/pull/1917
* [3.x] Fix ordering and implementation of setupcontroller. by @haringsrob in https://github.com/area17/twill/pull/1927
* [3.x] Fix like-operator usage. by @haringsrob in https://github.com/area17/twill/pull/1928
* [3.x] Add an autologin feature to improve DX by @antonioribeiro in https://github.com/area17/twill/pull/1904
* [3.x] Fixes for date picker. by @haringsrob in https://github.com/area17/twill/pull/1918
* [3.x] block wysiwyg title by @haringsrob in https://github.com/area17/twill/pull/1931
* [3x] custom icons sizes by @haringsrob in https://github.com/area17/twill/pull/1944
* [3.x] Fix admin app path so that it defaults to no path when using a subdom… by @haringsrob in https://github.com/area17/twill/pull/1940
* [3.x] Fix translatable metadata. by @haringsrob in https://github.com/area17/twill/pull/1942
* [3.x] Fix moduleRoute() double dots by @haringsrob in https://github.com/area17/twill/pull/1951
* [3.x] #1950 3x fix counter position by @haringsrob in https://github.com/area17/twill/pull/1953
* [3.x] Use TwillRoutes instead of macro's by @haringsrob in https://github.com/area17/twill/pull/1967
* [3.x] Slugs by @haringsrob in https://github.com/area17/twill/pull/1897
* Submodule improvements. by @haringsrob in https://github.com/area17/twill/pull/1954
* [3.x] Apply #1975: Fix block with long dynamic titles by @ptrckvzn in https://github.com/area17/twill/pull/1982
* Fix user roles down migration (preventing Dusk tests from running) by @joyceverheije in https://github.com/area17/twill/pull/1981
* 1955: Honour position/order in nested blocks by @mikerockett in https://github.com/area17/twill/pull/1956
* Fix restoreRevision not showing any fields in Content by @deckchan in https://github.com/area17/twill/pull/1971
* [3.x][bug] Check for user management when building navigation. by @haringsrob in https://github.com/area17/twill/pull/1984
* [3.x] Align code style by @haringsrob in https://github.com/area17/twill/pull/1990
* [3.x] Fix permissions for capsules. by @haringsrob in https://github.com/area17/twill/pull/1989
* [3.x] Fix active navigation with nested items. by @haringsrob in https://github.com/area17/twill/pull/1991
* Docs content update - boolean keyword by @kylegoines in https://github.com/area17/twill/pull/1983
* Adding style enhancments to docs UI by @kylegoines in https://github.com/area17/twill/pull/1974
* [3.x] BlockRenderer: Enable the rendering of site settings nested blocks. by @haringsrob in https://github.com/area17/twill/pull/1988
* [3.x] Fix tags 3z by @haringsrob in https://github.com/area17/twill/pull/1992
* Fix ordering of layers. by @haringsrob in https://github.com/area17/twill/pull/1996
* [3.x] Capsules/packages fixes: permissions compatibility and route macros by @joyceverheije in https://github.com/area17/twill/pull/1997
* Fix z-index publisher overlay by @joyceverheije in https://github.com/area17/twill/pull/2000
* [3.x] Set browser sortable to true by default by @joyceverheije in https://github.com/area17/twill/pull/1998
* [3.x] Form utils support in Twill 3's controller form definitions by @ifox in https://github.com/area17/twill/pull/1963
* QA updates for twill docs by @kylegoines in https://github.com/area17/twill/pull/2002
* Developer experience fixes by @haringsrob in https://github.com/area17/twill/pull/2006
* [3.x] Add support for default values. by @haringsrob in https://github.com/area17/twill/pull/2001
* [3.x] Docs improvements by @haringsrob in https://github.com/area17/twill/pull/1995
* [3.x] UI fixes by @haringsrob in https://github.com/area17/twill/pull/2013
* [3.x] #2015 preview hydration by @haringsrob in https://github.com/area17/twill/pull/2021
* [3.x] #2017: Support cached routes. by @haringsrob in https://github.com/area17/twill/pull/2020
* [3.x] Singleton - Add support of capsule inside the seed method by @cambad in https://github.com/area17/twill/pull/2019
* [3.x] #2019 tests by @haringsrob in https://github.com/area17/twill/pull/2027
* dynamic pluck key by @haringsrob in https://github.com/area17/twill/pull/2035
* [3.x] Make A17Block to be configurable class by @deckchan in https://github.com/area17/twill/pull/2018
* fix(2fa): qrcode generation by @tuanbinhtran in https://github.com/area17/twill/pull/2012
* [3.x] Fix Revision regressions by @haringsrob in https://github.com/area17/twill/pull/2010
* [3.x] Fix collapsing issue with repeaters. by @haringsrob in https://github.com/area17/twill/pull/2037
* Fix vselect float values 3x by @haringsrob in https://github.com/area17/twill/pull/2048
* [3.x] Block components. by @haringsrob in https://github.com/area17/twill/pull/2007
* [3.x] Document and add test helpers. by @haringsrob in https://github.com/area17/twill/pull/1932
* [3.x] #1957: Fix keepalive. by @haringsrob in https://github.com/area17/twill/pull/2043
* [3.x] Fix case sensitivity. by @haringsrob in https://github.com/area17/twill/pull/2049
* [3.x] Add ability to accepts parameters when using BladePartial directive by @sauron in https://github.com/area17/twill/pull/2045
* [3.x] Docs updates 2 by @haringsrob in https://github.com/area17/twill/pull/2052
* #2050: Fix media tags not saving on time. by @haringsrob in https://github.com/area17/twill/pull/2051
* Fix connected fields nesting + fix inpage nav. by @haringsrob in https://github.com/area17/twill/pull/2058
* [capsules] Default to psr4 valid path. by @haringsrob in https://github.com/area17/twill/pull/2057
* Dynamic repeaters bugfix by @haringsrob in https://github.com/area17/twill/pull/2061
* [3.x] Revert the change that updates the config / navigation files and just… by @haringsrob in https://github.com/area17/twill/pull/2062
* [3.x] Password reset new existing users by @haringsrob in https://github.com/area17/twill/pull/2070
* [3.x] Fix modules, webpack 5, live/hot reloading. by @haringsrob in https://github.com/area17/twill/pull/2069
* Fix site-link key by @vladimirmartsul in https://github.com/area17/twill/pull/2071
* [3.x] user activity by @haringsrob in https://github.com/area17/twill/pull/2063
* 1929 3x by @haringsrob in https://github.com/area17/twill/pull/2076
* [3.x] Multi select as tags by @haringsrob in https://github.com/area17/twill/pull/2068
* Improve block components. by @haringsrob in https://github.com/area17/twill/pull/2083
* Add blockIdentifier. by @haringsrob in https://github.com/area17/twill/pull/2085
* [3.x] Fix blocks related browser clearing by @joyceverheije in https://github.com/area17/twill/pull/2087
* [3.x] Make `skipCreateModal` compatible with Twill 3 form builder by @joyceverheije in https://github.com/area17/twill/pull/2088
* [3.x] Fix browser endpoint when edit route is not resolved by @joyceverheije in https://github.com/area17/twill/pull/2089
* [3.X] Dashboard pagination by @haringsrob in https://github.com/area17/twill/pull/2078
* [3.x] Fix password resets constraint down migration by @joyceverheije in https://github.com/area17/twill/pull/2093
* [3.x] Settings accessor by @haringsrob in https://github.com/area17/twill/pull/2095
* [3.x] Fix media library check. by @haringsrob in https://github.com/area17/twill/pull/2094
* [3.x] Allow translations for settings. by @haringsrob in https://github.com/area17/twill/pull/2092
* [3.x] tiptap 2 + custom link modal by @haringsrob in https://github.com/area17/twill/pull/2080
* Minor docs style fixes by @kylegoines in https://github.com/area17/twill/pull/2096
* [3.x] Cleanup password resets table before applying constraint by @joyceverheije in https://github.com/area17/twill/pull/2099
* Added option to also run db migrations when running twill:update command by @pauldwight in https://github.com/area17/twill/pull/2107
* [3.x] Fix small styling issue with blocks. by @haringsrob in https://github.com/area17/twill/pull/2106
* [3.x] Load all additional/custom components at any type. by @haringsrob in https://github.com/area17/twill/pull/2104
* Style updates for single-select by @kylegoines in https://github.com/area17/twill/pull/2098
* Style updates to fix multiselect regression by @kylegoines in https://github.com/area17/twill/pull/2097
* [3.x] Check element size to consider sidebar it sticky or not. by @haringsrob in https://github.com/area17/twill/pull/2105
* [3.x] 2101 link parser by @haringsrob in https://github.com/area17/twill/pull/2103
* Allow configuring crops from block components. by @haringsrob in https://github.com/area17/twill/pull/2115
* [3.x] Fix form builder check to allow not using the default content fieldset by @joyceverheije in https://github.com/area17/twill/pull/2117
* [3.x] Allow capsules to register blocks and repeaters by @haringsrob in https://github.com/area17/twill/pull/2109
* Allow disabling automatic navigation for Capsule packages by @haringsrob in https://github.com/area17/twill/pull/2126
* [3.x] #2120 block improvements by @haringsrob in https://github.com/area17/twill/pull/2124
* [3.x] Fix return type by @aksiome in https://github.com/area17/twill/pull/2140
* [3.x] Fix new docs on mobile by @ifox in https://github.com/area17/twill/pull/2136
* [3.x] Fix conflicts between native Twill blocks and app/vendor blocks by @ifox in https://github.com/area17/twill/pull/2135
* [3.x] Fix group / roles. by @haringsrob in https://github.com/area17/twill/pull/2133
* [3.x] 2111 bugfix table actions by @haringsrob in https://github.com/area17/twill/pull/2129
* [3.x] Fix getFullUrl localize by @haringsrob in https://github.com/area17/twill/pull/2142
* [3.x] Add multiple related browsers pointing to the same module docs. by @haringsrob in https://github.com/area17/twill/pull/2144
* [3.x] Add closure for options. by @haringsrob in https://github.com/area17/twill/pull/2143
* Fix some phpdocs by @joyceverheije in https://github.com/area17/twill/pull/2152
* Enable resend email only when user is published by @joyceverheije in https://github.com/area17/twill/pull/2169
* Laravel 10 support. by @haringsrob in https://github.com/area17/twill/pull/2170
* #2044|#1818: Improve repeater cloning. by @haringsrob in https://github.com/area17/twill/pull/2160
* Add fromArray to options by @aksiome in https://github.com/area17/twill/pull/2163
* #2154: Use scope. by @haringsrob in https://github.com/area17/twill/pull/2161
* Adding accordion UI to sidebar by @kylegoines in https://github.com/area17/twill/pull/2150
* Fix stretched image in browser field by @joyceverheije in https://github.com/area17/twill/pull/2199
* float min, max and step props for numeric input by @iedex in https://github.com/area17/twill/pull/2188
* it translation: missing string and fixes by @LucaRed in https://github.com/area17/twill/pull/2185
* Bump webpack from 5.75.0 to 5.76.1 by @dependabot in https://github.com/area17/twill/pull/2183
* Add source_path_prefix to Glide ServerFactory by @matteovg7 in https://github.com/area17/twill/pull/2131
* Allow using 0 as min or max for numeric input by @iedex in https://github.com/area17/twill/pull/2189
* ~ | Doc: add missing type declaration / change fieldsGroupsFormFieldN… by @agnonym in https://github.com/area17/twill/pull/2202
* Docs home blog and guides styling updates by @13twelve in https://github.com/area17/twill/pull/2187
* [3.x] Fix render for blocks by @haringsrob in https://github.com/area17/twill/pull/2192
* [3.x] Avoid recreating related items. by @haringsrob in https://github.com/area17/twill/pull/2198
* [3.x] Prefix tables with twill_ by @aksiome in https://github.com/area17/twill/pull/2195
* #2206: Fix migrations.: by @haringsrob in https://github.com/area17/twill/pull/2207
* Use correct messages + remove box. by @haringsrob in https://github.com/area17/twill/pull/2205
* WIP Example - basic page builder - updates by @13twelve in https://github.com/area17/twill/pull/2200
* Fix source edit not updateing the editor (TipTap) by @iedex in https://github.com/area17/twill/pull/2212
* fixing mistypes in ru localization by @Quarasique in https://github.com/area17/twill/pull/2208
* [3.x] Fix duplicate block duplicate with children by @agnonym in https://github.com/area17/twill/pull/2216
* [3.x] Ignore missing or disabled modules/capsules for permissions by @antonioribeiro in https://github.com/area17/twill/pull/2165
* [3.x] Fix duplicate action redirect route for nested parent-child mod… by @agnonym in https://github.com/area17/twill/pull/2215
* Release 3.0.0 test run by @ifox in https://github.com/area17/twill/pull/2217

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
