## Preface

### About Twill

Twill is an open source Laravel package that helps developers rapidly create a custom CMS that is beautiful, powerful, and flexible. By standardizing common functions without compromising developer control, Twill makes it easy to deliver a feature-rich admin console that focuses on modern publishing needs.

Twill is an [AREA 17](https://area17.com) product. It was crafted with the belief that content management should be a creative, productive, and enjoyable experience for both publishers and developers.

### Benefits overview

With a vast number of pre-built features and custom-built Vue.js UI components, developers can focus their efforts on the unique aspects of their applications instead of rebuilding standard ones. 

Built to get out of your way, Twill offers:
- No lock-in, create your data models or hook existing ones
- No front-end assumptions, use it within your Laravel app or headless
- No bloat, turn off features you don’t need
- No need to write/adapt HTML for the admin UI
- No limits, extend as you see fit


### Feature list

#### CRUD modules
* Enhanced Laravel “resources” models
* Command line generator and conventions to speed up creating new ones
* Based on PHP traits and regular Laravel concepts (migrations, models, controllers, form requests, repositories, Blade views)
* Fully custom forms per content type
* Slug management, including the ability to automatically redirect old urls
* Configurable content listings with searching, filtering, sorting, publishing, featuring, reordering and more
* Support for all Eloquent ORM relationships (1-1, 1-n, n-n, polymorphic)
* Content versioning

#### UI Components
* Large library of plugged in Vue.js form components with tons of options for maximum flexibility and composition
* Completely abstracted HTML markup. You’ll never have to deal with Bootstrap HTML again, which means you won’t ever have to maintain frontend related code for your CMS
* Input, text area, rich text area form fields with option to set SEO optimized limits
* Configurable WYSIWYG built with Quill.js
* Inline translated fields with independent publication status (no duplication)
* Select, multi-select, content type browsers for related content and tags
* Form repeaters
* Date and color pickers
* Flexible content block editor (dynamically composable from all form components)
* Custom content blocks per content type

#### Media library
* Media/files library with S3 and imgix integration (3rd party services are swappable)
* Image selector with smart cropping
* Ability to set custom image requirements and cropping parameters per content type
* Multiple crops possible per image for art directed responsive
* Batch uploading and tagging
* Metadata editing (alternative text, caption)
* Multi fields search (filename, alternative text, tags, dimensions…)

#### Configuration based features
* User authentication, authorization and management
* Fully configurable CMS navigation, with three levels of hierarchy and breadcrumbs for limitless content structure
* Configurable CMS dashboard with quick access links, activity log and Google Analytics integration
* Configurable CMS global search
* Intuitive content featuring, using a bucket UI. Put any of your content types in "buckets" to manage any layout of featured content or other concepts like localization

#### Developer experience
* Maintain a Laravel application, not a Twill application
* Support for Laravel 5.3 to 5.7 and will be updated to support all future versions
* Support for both MySQL and PostgreSQL databases
* No conflict with other Laravel packages – keep building with your tools of choice
* No specific server requirements, if you can deploy a Laravel application, you can deploy Twill
* Development and production ready toolset (debug bar, inspector, exceptions handler)
* No data lock in – all Twill content types are proper relational database tables, so it’s easy to move to Twill from other solutions and to expose content created with your Twill CMS to other applications
* Previewing and side by side comparison of fully rendered frontend site that you’ll get up and running very quickly no matter how you built your frontend (fully headed Laravel app, hybrid Laravel app with your own custom API endpoints or even full SPA with frameworks like React or Vue)
* Scales to very large amount of content without performance drawbacks, even on minimal resources servers (for what it’s worth, it’s running perfectly fine on a $5/month VPS, and you can cache frontend pages if you’d like through packages like laravel-response-cache or a CDN like Cloudfront)


### Credits

Over the last 15 years, nearly every engineer at AREA 17 has contributed to Twill in some capacity. The current iteration of Twill as an open source initiative was created by:

- [Quentin Renard](https://area17.com/about/quentin-renard), lead application engineer
- [Antoine Doury](https://area17.com/about/antoine-doury), lead interface engineer
- [Antonin Caudron](https://area17.com/about/antonin-caudron), interface engineer
- [Martin Rettenbacher](https://area17.com/about/martin-rettenbacher), product designer
- [Jesse Golomb](https://area17.com/about/jesse-golomb), product owner
- [George Eid](https://area17.com/about/george-eid), product manager

Additional contributors include [Laurens van Heems](https://area17.com/about/laurens-van-heems), [Fernando Petrelli](https://area17.com/about/fernando-petrelli), [Gilbert Moufflet](https://area17.com/about/gilbert-moufflet), [Mubashar Iqbal](https://area17.com/about/mubashar-iqbal), [Pablo Barrios](https://area17.com/about/pablo-barrios), [Luis Lavena](https://area17.com/about/luis-lavena), and [Mike Byrne](https://area17.com/about/mike-byrne).

### Contribution guide

#### Bug reports and features submission
To submit an issue or request a feature, please do so on [Github](https://github.com/area17/twill/issues).

If you file a bug report, your issue should contain a title and a clear description of the issue. You should also include as much relevant information as possible and a code sample that demonstrates the issue. The goal of a bug report is to make it easy for yourself - and others - to replicate the bug and develop a fix.

Remember, bug reports are created in the hope that others with the same problem will be able to collaborate with you on solving it. Do not expect that the bug report will automatically see any activity or that others will jump to fix it. Creating a bug report serves to help yourself and others start on the path of fixing the problem.

#### Security vulnerabilities
If you discover a security vulnerability within Twill, please email us at [security@twill.io](mailto:security@twill.io). All security vulnerabilities will be promptly addressed.

#### Versioning scheme
Twill's versioning scheme maintains the following convention: `paradigm.major.minor`. Minor releases should never contain breaking changes.

When referencing Twill from your application, you should always use a version constraint such as `1.2.*`, since major releases of Twill do include breaking changes.

The `VERSION` file at the root of the project needs to be updated and a Git tag created to properly release a new version.

#### Which branch?
All bug fixes should be sent to the latest stable branch (1.2). Bug fixes should never be sent to the master branch unless they fix features that exist only in the upcoming release.

Minor features that are fully backwards compatible with the current Twill release may be sent to the latest stable branch (1.2).

Major new features should always be sent to the master branch, which contains the upcoming Twill release.

Please send coherent history — make sure each individual commit in your pull request is meaningful. If you had to make a lot of intermediate commits while developing, please [squash them](http://www.git-scm.com/book/en/v2/Git-Tools-Rewriting-History#Changing-Multiple-Commit-Messages) before submitting.

#### Coding style
- PHP: [PSR-2 Coding Standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md).

- Javascript: [Standard](https://standardjs.com/), [Vue ESLint Essentials](https://github.com/vuejs/eslint-plugin-vue).

### Licensing
#### Software
The Twill software is licensed under the [Apache 2.0 license](https://www.apache.org/licenses/LICENSE-2.0.html).

#### User interface
The Twill UI, including but not limited to images, icons, patterns, and derivatives thereof are licensed under the [Creative Commons Attribution 4.0 International License](https://creativecommons.org/licenses/by/4.0/). 

#### Attribution
By using the Twill UI, you agree that any application which incorporates it shall prominently display the message “Made with Twill” in a legible manner in the footer of the admin console. This message must open a link to Twill.io when clicked or touched. For permission to remove the attribution, contact us at [hello@twill.io](hello@twill.io).