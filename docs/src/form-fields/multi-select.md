---
pageClass: twill-doc
title: Multi select
---

# Multi select

![screenshot](/docs/_media/multiselectunpacked.png)

```php
@formField('multi_select', [
    'name' => 'sectors',
    'label' => 'Sectors',
    'min' => 1,
    'max' => 2,
    'options' => [
        [
            'value' => 'arts',
            'label' => 'Arts & Culture'
        ],
        [
            'value' => 'finance',
            'label' => 'Banking & Finance'
        ],
        [
            'value' => 'civic',
            'label' => 'Civic & Public'
        ],
        [
            'value' => 'design',
            'label' => 'Design & Architecture'
        ],
        [
            'value' => 'education',
            'label' => 'Education'
        ]
    ]
])
```

| Option      | Description                                                  | Type/values     | Default value |
| :---------- | :----------------------------------------------------------- | :-------------- | :------------ |
| name        | Name of the field                                            | string          |               |
| label       | Label of the field                                           | string          |               |
| min         | Minimum number of selectable options                         | integer         |               |
| max         | Maximum number of selectable options                         | integer         |               |
| options     | Array of options for the dropdown, must include _value_ and _label_ | array           |               |
| unpack      | Defines if the multi select will be displayed as an open list of options | true<br/>false  | true         |
| columns     | Aligns the options on a grid with a given number of columns  | integer         | 0 (off)       |
| searchable  | Filter the field values while typing                         | true<br/>false  | false         |
| note        | Hint message displayed above the field                       | string          |               |
| placeholder | Text displayed as a placeholder in the field                 | string          |               |
| required    | Displays an indicator that this field is required<br/>A backend validation rule is required to prevent users from saving | true<br/>false  | false         |
| disabled    | Disables the field                                           | true<br />false | false         |


There are several ways to implement a `multi_select` form field.

##### Multi select with static values
Sometimes you just have a set of values that are static.

In this case that it can be implemented as follows:

- Create the database migration to store a JSON or LONGTEXT:
```php
Schema::table('posts', function (Blueprint $table) {
    ...
    $table->json('sectors')->nullable();
    ...
});

// OR
Schema::table('posts', function (Blueprint $table) {
    ...
    $table->longtext('sectors')->nullable();
    ...
});
```

- In your model add an accessor and a mutator:
```php
public function getSectorsAttribute($value)
{
    return collect(json_decode($value))->map(function($item) {
        return ['id' => $item];
    })->all();
}

public function setSectorsAttribute($value)
{
    $this->attributes['sectors'] = collect($value)->filter()->values();
}
```

- Cast the field to `array`:
```php
protected $casts = [
    'sectors' => 'array'
]
```

##### Multi select with dynamic values

Sometimes the content for the `multi_select` is coming from another model.

In this case that it can be implemented as follows:

- Create a Sectors [module](https://twill.io/docs/#cli-generator)

```
php artisan twill:module sectors
```

- Create a migration for a pivot table.

```
php artisan make:migration create_post_sector_table
```

- Use Twill's `createDefaultRelationshipTableFields` to set it up:

```php
public function up()
{
    Schema::create('post_sector', function (Blueprint $table) {
        createDefaultRelationshipTableFields($table, 'sector', 'post');
        $table->integer('position')->unsigned()->index();
    });
}
```

- In your model, add a `belongsToMany` relationship:

```php
public function sectors() {
    return $this->belongsToMany('App\Models\Sector');
}
```

- In your repository, make sure to sync the association when saving:

```php
public function afterSave($object, $fields)
{
    $object->sectors()->sync($fields['sectors'] ?? []);

    parent::afterSave($object, $fields);
}
```

- In your controller, add to the formData the collection of options:
```php
protected function formData($request)
{
    return [
        'sectors' => app()->make(SectorRepository::class)->listAll()
    ];
}
```

- In the form, we can now add the field:
```php
@formField('multi_select', [
    'name' => 'sectors',
    'label' => 'Sectors',
    'options' => $sectors
])
```

When used in a [block](https://twill.io/docs/#adding-blocks), no migration is needed.

