# One to Many

[One to Many](https://laravel.com/docs/9.x/eloquent-relationships#one-to-many) can be used for having child models that
are referred to by your main model.

An example could be:

- A blog having comments
- An article having source links
- ...

One to Many is one of the simpler relations to setup so let's get started:

## Database setup

As with any relation we need to setup a database. In this example we are using our portfolio example where we will have
multiple links on a project.

We will set up 2 models, one is a Project model, you can do this using `php artisan twill:module Project`

And afterwards a Link model: `php artisan twill:module Link`, As the Link model is used for our hasMany, we do not have
to add it to the routes or navigation files, so you can ignore that suggestion.

In the **Link** migration we add a column to hold the `project_id` that we are creating it from.

:::filename:::
`database/migrations/2022_05_30_074255_create_links_tables.php`
:::#filename:::

```phptorch
{
  "file": "../../../../examples/portfolio/database/migrations/2022_05_30_074255_create_links_tables.php",
  "focusMethods": "up",
  "diffInMethod": {
    "method": "up",
    "start": 5,
    "end": 5
  }
}
```

## Define the relation

Now with the migration setup we can setup our relation in the `Project` model:

:::filename:::
`app/Models/Project.php`
:::#filename:::

```phptorch
{
  "file": "../../../../examples/portfolio/app/Models/Project.php",
  "collapseAll": "",
  "focusMethods": "links"
}
```

## Setup the repeater and form

To expose the relation in the ui, we will use a repeater.

:::filename:::
`resources/views/twill/repeaters/link.blade.php`
:::#filename:::

```phptorch
{
  "file": "../../../../examples/portfolio/resources/views/twill/repeaters/link.blade.php",
  "simple": true
}
```

In our project form we can now refer to the repeater and allow editors to select.

:::filename:::
`resources/views/twill/projects/form.blade.php`
:::#filename:::

```phptorch
{
  "file": "../../../../examples/portfolio/resources/views/twill/projects/form.blade.php",
  "simple": true
}
```

## Update the repository

As a final step we have to update the repository to map the repeater field to the relation.

:::filename:::
`app/Repositories/ProjectRepository.php`
:::#filename:::

```phptorch
{
  "file": "../../../../examples/portfolio/app/Repositories/ProjectRepository.php",
  "collapseAll": "",
  "focusMethods": ["afterSave", "getFormFields"],
  "diffInMethod": [
      {
        "method": "afterSave",
        "start": 10,
        "end": 14
      },
      {
        "method": "getFormFields",
        "start": 11,
        "end": 17
      }
  ]
}
```
