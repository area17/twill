---
pageClass: twill-doc
---

# BelongsToMany

[BelongsToMany](https://laravel.com/docs/9.x/eloquent-relationships#many-to-many) is a great way to make one model refer
to many others.

Examples could be:
- An order containing multiple products
- An artwork having multiple artists
- A project having multiple contributors

In addition to that we could use [pivot](https://laravel.com/docs/9.x/eloquent-relationships#retrieving-intermediate-table-columns)
data to further extend this relation, to complete the examples above:

- Each product in the order can have an order specific price
- Each artwork artist may have focused on something specific
- Each contributor might have worked on a different part of a project

In Twill we can set this up using a [repeater](/form-fields/repeaters.md) and below we will go thorough all the steps to
make this work, using the **project/contributor** example.

If you want to quickly test this in a new project, you can install twill using the portfolio example:
`php artisan twill:install portfolio`

## Database setup
