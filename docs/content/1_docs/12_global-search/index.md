# Global Search

By default, Twill's global search input is always available in the dashboard and behind the top-right search icon on other Twill's screens. By default, the search input performs a LIKE query on the title attribute only. If you like, you can specify a custom list of attributes to search for in each dashboard enabled module:

```php
return [
    'dashboard' => [
        'modules' => [
            'projects' => [
                'name' => 'projects',
                'routePrefix' => 'work',
                'count' => true,
                'create' => true,
                'activity' => true,
                'draft' => true,
                'search' => true,
                'search_fields' => ['name', 'description']
            ],
            ...
        ],
        ...
    ],
    ...
];
```

You can also customize the endpoint to handle search queries yourself:

```php
return [
    'dashboard' => [
        ...,
        'search_endpoint' => 'your.custom.search.endpoint.route.name',
    ],
    ...
];
```

You will need to return a collection of values, like in the following example:

```php
return $searchResults->map(function ($item) use ($module) {
    try {
        $author = $item->revisions()->latest()->first()->user->name ?? 'Admin';
    } catch (\Exception $e) {
        $author = 'Admin';
    }

    return [
        'id' => $item->id,
        'href' => moduleRoute($moduleName['name'], $moduleName['routePrefix'], 'edit', $item->id),
        'thumbnail' => $item->defaultCmsImage(['w' => 100, 'h' => 100]),
        'published' => $item->published,
        'activity' => 'Last edited',
        'date' => $item->updated_at->toIso8601String(),
        'title' => $item->title,
        'author' => $author,
        'type' => Str::singular($module['name']),
    ];
})->values();
```
