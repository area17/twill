# Changelog

All notable changes to `laravel-cms-toolkit` will be documented in this file.

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
