# One to Many

[One to Many](https://laravel.com/docs/10.x/eloquent-relationships#one-to-many) can be used for having child models that are referred to by your main model.

An example could be:

- A blog having comments
- An article having source links
- ...

One to Many is one of the simpler relations to set up so let's get started:

## Database setup

As with any relation we need to set up a database. In this example we are using our portfolio example where we will have multiple links on a project.

We will set up 2 models, one is a Project model, you can do this using `php artisan twill:module Project`

And afterwards a Link model: `php artisan twill:module Link`, As the Link model is used for our hasMany, we do not have to add it to the routes or navigation files, so you can ignore that suggestion.

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

Now with the migration setup we can set up our relation in the `Project` model:

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

## Set up the repeater form

In our project form we can now create an inline repeater.

:::filename:::
`app/Http/Controllers/Twill/ProjectController.php`
:::#filename:::

```phptorch
{
  "file": "../../../../examples/portfolio/app/Http/Controllers/Twill/ProjectController.php",
  "collapseAll": "",
  "focusImports": ["A17\\Twill\\Services\\Forms\\InlineRepeater"],
  "diffImports": ["A17\\Twill\\Services\\Forms\\InlineRepeater"],
  "focusMethods": "getForm",
  "diffInMethod": {
    "method": "getForm",
    "start": 23,
    "end": 32
  }
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
        "start": 11,
        "end": 15
      },
      {
        "method": "getFormFields",
        "start": 12,
        "end": 18
      }
  ]
}
```
