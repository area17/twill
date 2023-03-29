# Field grouping

Twill supports grouping fields in the database using a json column.

Examples:

- An address section existing out of a street, city and postal code field
- An external link with Label, url and target
- ...

Each set of grouped fields requires a column in the database. The grouped fields can be translatable or not.

## Migration and Model setup

The migration for adding a grouped field can look like this (using external link as example):

### Translatable

```php
## Add a migration
Schema::table('blog_translations', function (Blueprint $table) {
    $table->json('external_link')->nullable();
});

## Update your models fillable
class Blog extends Model {
    public $translatedAttributes = [
        ...
        'external_link'
    ];

    protected $fillable = [
        ...
        'external_link'
    ];
}

## Update your Translation model and add the cast
public $casts = [
    'external_link' => 'array',
];
```

### Non translatable

```php
## Add a migration
Schema::table('blogs', function (Blueprint $table) {
    $table->json('external_link')->nullable();
});

## Update your models fillable
class Blog extends Model {
    protected $fillable = [
        ...
        'external_link'
    ];
}
```

## Field setup

To store the fields you want into the json we have to update the repository:

```php
protected array $fieldsGroups = [
    'external_link' => [
        'link_target',
        'link_url',
        'link_label',
    ],
];

# The below can be setup optionally, documented below.
public bool $fieldsGroupsFormFieldNamesAutoPrefix = false;
public string $fieldsGroupsFormFieldNameSeparator = '_';
```

Finally in our model form we can add the fields:

```blade
<x-twill::input
    name="link_target"
    label="Link target"
    :translated="true"
/>

<x-twill::input
    name="link_url"
    label="Link url"
    :translated="true"
/>

<x-twill::input
    name="link_label"
    label="Link label"
    :translated="true"
/>
```

### Using the field name separator

In the repository file you can setup the following parameters:

```php
public bool $fieldsGroupsFormFieldNamesAutoPrefix = true;
public string $fieldsGroupsFormFieldNameSeparator = '-'; // Default is _
```

This will automatically group/ungroup these fields based on the separator:

```blade
<x-twill::input
    name="external_link.link_target"
    label="Link target"
    :translated="true"
/>

<x-twill::input
    name="external_link.link_url"
    label="Link url"
    :translated="true"
/>

<x-twill::input
    name="external_link.link_label"
    label="Link label"
    :translated="true"
/>
```
