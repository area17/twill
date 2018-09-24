# Changelog

All notable changes to `twill` will be documented in this file.

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
