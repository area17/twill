# Feature List

## CRUD modules

* Enhanced Laravel “resources” models
* Command line generator and conventions to speed up creating new ones
* Based on PHP traits and regular Laravel concepts (migrations, models, controllers, form requests, repositories, Blade
  views)
* Fully custom forms per content type
* Slug management, including the ability to automatically redirect old urls
* Configurable content listings with searching, filtering, sorting, publishing, featuring, reordering and more
* Support for all Eloquent ORM relationships (1-1, 1-n, n-n, polymorphic)
* Content versioning

## UI Components

* Large library of plugged-in Vue.js form components with tons of options for maximum flexibility and composition
* Completely abstracted HTML markup. You’ll never have to deal with Bootstrap HTML again, which means you won’t ever
  have to maintain frontend-related code for your CMS
* Input, text area, rich text area form fields with option to set SEO optimized limits
* Configurable WYSIWYG built with Quill.js
* Inline translated fields with independent publication status (no duplication)
* Select, multi-select, content type browsers for related content and tags
* Form repeaters
* Date and color pickers
* Flexible content block editor (dynamically composable from all form components)
* Custom content blocks per content type

## Media library

* Media/files library with S3 and imgix integration (3rd party services are swappable)
* Image selector with smart cropping
* Ability to set custom image requirements and cropping parameters per content type
* Multiple crops possible per image for art directed responsive
* Batch uploading and tagging
* Metadata editing (alternative text, caption)
* Multi fields search (filename, alternative text, tags, dimensions…)

## Configuration based features

* User authentication, authorization and management
* Fully configurable CMS navigation, with three levels of hierarchy and breadcrumbs for limitless content structure
* Configurable CMS dashboard with quick access links, activity log and Google Analytics integration
* Configurable CMS global search
* Intuitive content featuring, using a bucket UI. Put any of your content types in "buckets" to manage any layout of
  featured content or other concepts like localization

## Developer experience

* Maintain a Laravel application, not a Twill application
* Support for Laravel 5.6 and up – Twill will be updated to support all future versions
* Support for both MySQL and PostgreSQL databases
* No conflict with other Laravel packages – keep building with your tools of choice
* No specific server requirements, if you can deploy a Laravel application, you can deploy Twill
* Development and production ready toolset (debug bar, inspector, exceptions handler)
* No data lock in – all Twill content types are proper relational database tables, so it’s easy to move to Twill from
  other solutions and to expose content created with your Twill CMS to other applications
* Previewing and side by side comparison of fully rendered frontend site that you’ll get up and running very quickly no
  matter how you built your frontend (fully headed Laravel app, hybrid Laravel app with your own custom API endpoints or
  even full SPA with frameworks like React or Vue)
* Scales to very large amount of content without performance drawbacks, even on minimal resources servers (for what it’s
  worth, it’s running perfectly fine on a $5/month VPS, and you can cache frontend pages if you’d like through packages
  like laravel-response-cache or a CDN like Cloudfront)
