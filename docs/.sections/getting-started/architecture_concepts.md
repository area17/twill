## Getting started

### Architecture concepts

#### CRUD modules

A Twill [CRUD module](#crud-modules-3) is a set of classes and configurations in your Laravel application that are going to enable your admin users to manage a certain type of content, for which the content structure is completely up to you. 

A module can be seen as a more feature rich Laravel resource. For non Laravel developer, this is basically a content type (or post type, as often seen in other CMS solutions) with CRUD operations (Create, Read, Update, Delete) as well as custom Twill provided operations like Publish, Feature, Tag, Preview, Restore, Restore revision, Reorder or Bulk edit .

In Twill's UI, a module mainly consists of a listing page and a form page or modal.

Records created under a module can then be associated with another or multiple module's records to create relationships between your content. 

Technically, you will store Twill's module records and their associations in a traditionnal relational database schema following Laravel's migrations and Eloquent model conventions.

Twill's modules features are made available using PHP traits you include to your Eloquent models and Twill's repositories, various configuration variables, and a bunch of conventions to follow, documented in the following section of this documentation.

A Twill module can accomodate numerous variations of content fields, organization and structure, as it is completely up to you to setup by composing a form using all Twill's available form fields.

Conceptually, depending on how you configure a Twill module, we've identified the following types of content you can create:

*Entities*

Entities ar your primary data models most often having listing and detail screens associated to them in your frontend. For example, blog posts, projects, people, etc. 

In a CMS, they need an advanced listing screen and a more or less complex form screen. This is the default behavior of a Twill module.

*Attributes*

Attributes are secondary data models most often used to add structured details to an entity (for search, filtering, and/or display). For example, categories, types, sectors, industries, etc.

In a CMS, they need a listing screen and quick creation and edition ability across all forms as needed. As those tend to be relatively simple in content structure, their form screen can fit in a modal that is made available from other parts of the CMS than only from their own listing screen. In Twill, the `editInModal` index option of your module's controllers can be used to enabled that behavior.

*Pages*

Pages are unstructured data models most often used for static content such as an about section. Rather than listing and detail screens, pages are manually organized into parent/child relationships for navigation by section. 

Combined with the [kalnoy/nestedset](https://github.com/lazychaser/laravel-nestedset) package, a Twill's module can be configured to show and make parent/child relationships manageable on a module's records.

*Elements*

Elements are modules or snippets of content that are added to an entity, page, or screen. For example the ability to manage footer text, create a global alert that can be turned on/off, etc. 

Twill offers developers the ability to quickly create [settings sections](#settings-sections) to manage those pieces of content. A Twill module could also be configured to manage any sort of standalone piece or composition of content. There's nothing wrong in having a database table with a single record if that is what your content requirements dictates, so you should feel free to create a Twill module to have a custom form for a single record. You can use a Laravel seeder or migration to initialize your admin console with those records.

#### Fully custom CMS navigation organization

*At AREA 17, we like to map the navigation of CMSs we create to the navigation of the public facing website they serve...?*

#### Block editor
*Add blurb about what is/why the block editor*

A block is a composition of form fields made available to content editors in Twill's block editor form field. This is usually used when giving content editors the ability to create content freely for a specific part of a page. Example blocks are: title, text, quote, image, slideshow, related content. Anything you can think of really by composing from all available form fields.

#### Buckets
*Add blurb about buckets?*

