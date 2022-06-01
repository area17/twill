---
pageClass: twill-doc
---

# One to Many

[One to Many](https://laravel.com/docs/9.x/eloquent-relationships#one-to-many) can be used for having child models that
are referred to by your main model.

An example could be:

- A blog having comments
- An article having source links
- ...

One to Many is one of the simpler relations to setup so let's get started:

[[toc]]

## Database setup

As with any relation we need to setup a database. In this example we are using our portfolio example where we will have
multiple links on a project.

We will set up 2 models, one is a Project model, you can do this using `php artisan twill:module Project`

And afterwards a Link model: `php artisan twill:module Link`, As the Link model is used for our hasMany, we do not have
to add it to the routes or navigation files, so you can ignore that suggestion.

In the **Link** migration we add a column to hold the `project_id` that we are creating it from.

`database/migrations/2022_05_30_074255_create_links_tables.php`

<<< @/src/../../examples/portfolio/database/migrations/2022_05_30_074255_create_links_tables.php{15}

## Define the relation

Now with the migration setup we can setup our relation in the `Project` model:

`app/Models/Project.php`

<<< @/src/../../examples/portfolio/app/Models/Project.php{35-38}

## Setup the repeater and form

To expose the relation in the ui, we will use a repeater.

`resources/views/twill/repeaters/link.blade.php`

<<< @/src/../../examples/portfolio/resources/views/twill/repeaters/link.blade.php

In our project form we can now refer to the repeater and allow editors to select.

<<< @/src/../../examples/portfolio/resources/views/twill/projects/form.blade.php{31-32}

## Update the repository

As a final step we have to update the repository to map the repeater field to the relation.

`app/Repositories/ProjectRepository.php`

<<< @/src/../../examples/portfolio/app/Repositories/ProjectRepository.php{33-37,62-68}
