# Buckets

Twill's buckets allow you to provide publishers with featured content management screens. You can add multiple pages of buckets anywhere you'd like in your CMS navigation and, in each page, multiple buckets with different rules and accepted modules. In the following example, we will assume that our application has a Guide model and that we want to feature guides on the homepage of our site. Our site's homepage has multiple zones for featured guides: a primary zone, that shows only one featured guide, and a secondary zone, that shows guides in a carousel of maximum 10 items.

First, you will need to enable the buckets feature. In `config/twill.php`:

```php
'enabled' => [
    'buckets' => true,
],
```

Then, define your buckets' configuration:

```php
'buckets' => [
    'homepage' => [
        'name' => 'Home',
        'buckets' => [
            'home_primary_feature' => [
                'name' => 'Home primary feature',
                'bucketables' => [
                    [
                        'repository' => GuidesRepository::class,
                        'module' => 'guides',
                        'name' => 'Guides',
                        'scopes' => ['published' => true],
                    ],
                ],
                'max_items' => 1,
            ],
            'home_secondary_features' => [
                'name' => 'Home secondary features',
                'bucketables' => [
                    [
                        'repository' => GuidesRepository::class,
                        'module' => 'guides',
                        'name' => 'Guides',
                        'scopes' => ['published' => true],
                    ],
                ],
                'max_items' => 10,
            ],
        ],
    ],
],
```

You can allow mixing modules in a single bucket by adding more modules to the `bucketables` array.
We recommend that each `bucketable` model be in the [morph map](https://laravel.com/docs/10.x/eloquent-relationships#polymorphic-relationships) because features are stored in a polymorphic table.

In your AppServiceProvider, you can do it like the following:

```php
use Illuminate\Database\Eloquent\Relations\Relation;
...
public function boot()
{
    Relation::morphMap([
        'guides' => App\Models\Guide::class,
    ]);
}
```

Finally, add a link to your buckets page in your CMS navigation:

```php
return [
   'featured' => [
       'title' => 'Features',
       'route' => 'twill.featured.homepage',
       'primary_navigation' => [
           'homepage' => [
               'title' => 'Homepage',
               'route' => 'admin.featured.homepage',
           ],
       ],
   ],
   ...
];
```

By default, the buckets page (in our example, only homepage) will live under the /featured prefix.
But you might need to split your buckets page between sections of your CMS. For example if you want to have the homepage bucket page of our example under the /pages prefix in your navigation, you can use another configuration property:

```php
'bucketsRoutes' => [
    'homepage' => 'pages'
]
```
