---
pageClass: twill-doc
---

# Prefill a block editor from a selection of templates

Objectives:

- Create a new module with a `template` field
- Prefill the block editor for new items according to the selected template

Versions used at the time of writing:

|            | Version |
|:-----------|:-------:|
| PHP        | 8.0     |
| Laravel    | 8.61    |
| Twill      | 2.5.2   |

## Create the new module

```
php artisan twill:make:module articles -B
```

We'll make sure to enable blocks on the module, everything else is optional. In this example, we won't be using
translations, but they can be added with minor changes.


## Update the migration

We'll add the `template` field to the generated migration:

<<< @/src/guides/prefill-block-editor-from-template/2021_09_19_131244_create_articles_tables.php{16}

Then, we'll run the migrations:
```
php artisan migrate
```

and add the module to our `routes/admin.php` and `config/twill-navigation.php`.


## Update the model

In this example, we imagine 3 templates that our authors can choose from:

- Full Article: an original article on our blog
- Linked Article: a short article to summarize and share interesting articles from other blogs
- Empty: a blank canvas


We'll start by adding our new field to the fillables:

`app/Models/Article.php`

<<< @/src/guides/prefill-block-editor-from-template/Article.php#fillable{6}

Then, we'll define some constants for our template options:

<<< @/src/guides/prefill-block-editor-from-template/Article.php#constants

We'll add an attribute accessor to get the template name for the currently selected template value:

<<< @/src/guides/prefill-block-editor-from-template/Article.php#accessor

This will be useful in our `create.blade.php` view below.

## Add the `template` field to the create modal

When running `php artisan twill:make:module`, we get a `form.blade.php` to define the main form for our module. In addition, it's also possible to redefine the fields that are displayed in the create modal, before the form:

![01-create-modal](./prefill-block-editor-from-template/create-modal.png)

We'll copy Twill's built-in view from `vendor/area17/twill/views/partials/create.blade.php` into our project, then add our `template` field:

`resources/views/admin/articles/create.blade.php`

<<< @/src/guides/prefill-block-editor-from-template/articles_create.blade.php

## Create some blocks

```
php artisan twill:make:block article-header
php artisan twill:make:block article-paragraph
php artisan twill:make:block article-references
php artisan twill:make:block linked-article
```

::: details blocks/article-header.blade.php
<<< @/src/guides/prefill-block-editor-from-template/blocks_article-header.blade.php
:::

::: details blocks/article-paragraph.blade.php
<<< @/src/guides/prefill-block-editor-from-template/blocks_article-paragraph.blade.php
:::

::: details blocks/article-references.blade.php
<<< @/src/guides/prefill-block-editor-from-template/blocks_article-references.blade.php
:::

::: details blocks/linked-post.blade.php
<<< @/src/guides/prefill-block-editor-from-template/blocks_linked-post.blade.php
:::

## Add the editor to our form

We'll add the block editor field to our form:

`resources/views/admin/articles/form.blade.php`

<<< @/src/guides/prefill-block-editor-from-template/articles_form.blade.php{10-12}

## Prefill the blocks on create

With this, all that's needed is to initialize the block editor from the selected template. We'll update our model to add the prefill operation:

`app/Models/Article.php`

<<< @/src/guides/prefill-block-editor-from-template/Article.php#prefill

Then, we'll hook into the repository's `afterSave()`:

<<< @/src/guides/prefill-block-editor-from-template/ArticleRepository.php{22-29}

The check on `$object->wasRecentlyCreated` ensures the prefill operation will only run when the record is first created.

## Finished result

And there we have it Ñ a templating mechanism for our block editor:

![02-edit-form](./prefill-block-editor-from-template/final.png)
