---
pageClass: twill-doc
---

# One to Many (Polymorphic)

[One to Many (Polymorphic)](https://laravel.com/docs/9.x/eloquent-relationships#one-to-many-polymorphic-relations) can be used to refer to a model that may 
not necessarily always be the same.

A great example of this would be having `blogs` and `news` models, and a `comment` model that references
either the `blog` or `news` model.

We will use a similar setup to the example above to set this up, 
we will not go in depth on the other modules but you can
view the full code used in this example at: `vendor/twill/examples/portfolio`

[[toc]]

## Database setup

On our database not much needs to happen. In our comments migration we have to add
a `nullableMorphs` that we will use to store the relational data.

A `morph` automatically creates a `{morphName}_id` and `{morphName}_type` that Laravel understands.

the `_id` will hold the model id we are referencing. `_type` will hold the type of the model we are
targeting.

`database/migrations/2022_04_06_070334_create_comments_tables.php`

<<< @/src/../../examples/portfolio/database/migrations/2022_04_06_070334_create_comments_tables.php{15}

## Define the relation

Now that our migration is in place we can move onward to our model setup. Here we will follow the
documentation Laravel again [model structure](https://laravel.com/docs/9.x/eloquent-relationships#one-to-many-polymorphic-model-structure).

`app/Models/Comment.php`

<<< @/src/../../examples/portfolio/app/Models/Comment.php{16-19}

As our database column is named the same as our method, we do not have to specify anything
else in the `->morphTo` method.

### Defining the inverse

In the example we allow comments on `Partners` and `Projects`, so what we need to do now is
define the **inverse** of this relation in each of these models:

`app/Models/Partner.php`

<<< @/src/../../examples/portfolio/app/Models/Partner.php{32-35}

`app/Models/Project.php`

<<< @/src/../../examples/portfolio/app/Models/Project.php{39-42}

Now that we have our inverse set up we can continue setting up our repository.

## Update the repository

### Creating the repeater

We want to be able to add comments from both our Partner and Project module.

First we will create a repeater that we can use to create new comments on any of these modules

`resources/views/twill/repeaters/comment.blade.php`

<<< @/src/../../examples/portfolio/resources/views/twill/repeaters/comment.blade.php

Then in both `resources/views/twill/partners/form.blade.php` and `resources/views/twill/projects/form.blade.php`
we add:

```
@formField('repeater', ['type' => 'comment'])
```

When you now visit either of the forms you should see the new **Add comment** button.

### Configuring the repository

Next we have to update our repository so that when the form is submitted, the comment is saved properly.

For this we will use the method `updateRepeaterMorphMany` and `getFormFieldsForRepeater`.

`app/Repositories/PartnerRepository.php`

<<< @/src/../../examples/portfolio/app/Repositories/PartnerRepository.php{21-33,35-46}

Do the same for `app/Repositories/ProjectRepository.php` and then everything should be fully functional.

Do not forget to run your migrations if you run into any issues.

