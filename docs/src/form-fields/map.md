---
pageClass: twill-doc
---

# Map

![screenshot](/docs/_media/map.png)

```php
@formField('map', [
    'name' => 'location',
    'label' => 'Location',
    'showMap' => true,
])
```

| Option           | Description                                                 | Type/values     | Default value |
| :--------------- | :---------------------------------------------------------- | :-------------- | :------------ |
| name             | Name of the field                                           | string          |               |
| label            | Label of the field                                          | string          |               |
| showMap          | Adds a button to toggle the map visibility                  | true<br />false | true          |
| openMap          | Used with `showMap`, initialize the field with the map open | true<br />false | false          |
| saveExtendedData | Enables saving Bounding Box Coordinates and Location types  | true<br />false | false         |

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

When used in a [block](https://twill.io/docs/#adding-blocks), no migration is needed.

#### Example of data stored in the Database:
Default data:

```javascript
{
    "latlng": "48.85661400000001|2.3522219",
    "address": "Paris, France"
}
```

Extended data:

```javascript
{
    "latlng": "51.1808302|-2.256022799999999",
    "address": "Warminster BA12 7LG, United Kingdom",
    "types": ["point_of_interest", "establishment"],
    "boundingBox": {
        "east": -2.25289275,
        "west": -2.257066149999999,
        "north": 51.18158853029149,
        "south": 51.17889056970849
    }
}
```
