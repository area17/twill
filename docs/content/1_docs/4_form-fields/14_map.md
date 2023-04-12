# Map

![screenshot](/assets/map.png)

:::tabs=currenttab.FormBuilder&items.FormBuilder|FormView|Directive:::
:::tab=name.FormBuilder:::

```php
Map::make()
    ->name('location')
    ->openMap()
```

:::#tab:::
:::tab=name.FormView:::

```blade
<x-twill::map
    name="location"
    label="Location"
    :show-map="true"
/>
```

:::#tab:::
:::tab=name.Directive:::

```blade
@formField('map', [
    'name' => 'location',
    'label' => 'Location',
    'showMap' => true,
])
```

:::#tab:::
:::#tabs:::

| Option           | Description                                                 | Type/values | Default value |
|:-----------------|:------------------------------------------------------------|:------------|:--------------|
| name             | Name of the field                                           | string      |               |
| label            | Label of the field                                          | string      |               |
| showMap          | Adds a button to toggle the map visibility                  | boolean     | true          |
| openMap          | Used with `showMap`, initialize the field with the map open | boolean     | false         |
| saveExtendedData | Enables saving Bounding Box Coordinates and Location types  | boolean     | false         |

This field requires that you provide a `GOOGLE_MAPS_API_KEY` variable in your .env file.

A migration to save a `map` field would be:

```php
Schema::table('posts', function (Blueprint $table) {
    ...
    $table->json('location')->nullable();
    ...
});
```

The field used should also be casted as an array in your model:

```php
public $casts = [
    'location' => 'array',
];
```

When used in a [block](../5_block-editor), no migration is needed.

#### Example of data stored in the Database:

Default data:

```json
{
  "latlng": "48.85661400000001|2.3522219",
  "address": "Paris, France"
}
```

Extended data:

```json
{
  "latlng": "51.1808302|-2.256022799999999",
  "address": "Warminster BA12 7LG, United Kingdom",
  "types": [
    "point_of_interest",
    "establishment"
  ],
  "boundingBox": {
    "east": -2.25289275,
    "west": -2.257066149999999,
    "north": 51.18158853029149,
    "south": 51.17889056970849
  }
}
```
