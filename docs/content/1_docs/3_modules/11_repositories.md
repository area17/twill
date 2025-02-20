# Repositories

Depending on the model feature, include one or multiple of these traits: `HandleTranslations`, `HandleSlugs`, `HandleMedias`, `HandleFiles`, `HandleRevisions`, `HandleBlocks`, `HandleRepeaters`, `HandleTags`.

Repositories allows you to modify the default behavior of your models by providing some entry points in the form of methods that you might implement:

### Filtering

```php
<?php

// implement the filter method
public function filter($query, array $scopes = []) {

    // and use the following helpers

    // add a where like clause
    $this->addLikeFilterScope($query, $scopes, 'field_in_scope');

    // add orWhereHas clauses
    $this->searchIn($query, $scopes, 'field_in_scope', ['field1', 'field2', 'field3']);

    // add a whereHas clause
    $this->addRelationFilterScope($query, $scopes, 'field_in_scope', 'relationName');

    // or just go manually with the $query object
    if (isset($scopes['field_in_scope'])) {
      $query->orWhereHas('relationName', function ($query) use ($scopes) {
          $query->where('field', getLikeOperator(), '%' . $scopes['field_in_scope'] . '%');
      });
    }

    // don't forget to call the parent filter function
    return parent::filter($query, $scopes);
}
```

### Custom ordering

```php
<?php

// implement the order method
public function order($query, array $orders = []) {
    // don't forget to call the parent order function
    return parent::order($query, $orders);
}
```

### Custom form fields

```php
<?php

// implement the getFormFields method
public function getFormFields($object) {
    // don't forget to call the parent getFormFields function
    $fields = parent::getFormFields($object);

    // get fields for a browser
    $fields['browsers']['relationName'] = $this->getFormFieldsForBrowser($object, 'relationName');

    // get fields for a repeater
    $fields = $this->getFormFieldsForRepeater($object, $fields, 'relationName', 'ModelName', 'repeaterItemName');

    // return fields
    return $fields
}
```

### Custom field preparation before create action


```php
<?php

// implement the prepareFieldsBeforeCreate method
public function prepareFieldsBeforeCreate($fields) {
    // don't forget to call the parent prepareFieldsBeforeCreate function
    return parent::prepareFieldsBeforeCreate($fields);
}
```

### Custom field preparation before save action


```php
<?php

// implement the prepareFieldsBeforeSave method
public function prepareFieldsBeforeSave($object, $fields) {
    // don't forget to call the parent prepareFieldsBeforeSave function
    return parent:: prepareFieldsBeforeSave($object, $fields);
}
```

### After save actions (like attaching a relationship)

```php
<?php

// implement the afterSave method
public function afterSave($object, $fields) {
    // for exemple, to sync a many to many relationship
    $this->updateMultiSelect($object, $fields, 'relationName');

    // which will simply run the following for you
    $object->relationName()->sync($fields['relationName'] ?? []);

    // or, to save a oneToMany relationship
    $this->updateOneToMany($object, $fields, 'relationName', 'formFieldName', 'relationAttribute')

    // or, to save a belongToMany relationship used with the browser field
    $this->updateBrowser($object, $fields, 'relationName');

    // or, to save a hasMany relationship used with the repeater field
    $this->updateRepeater($object, $fields, 'relationName', 'ModelName', 'repeaterItemName');

    // or, to save a belongToMany relationship used with the repeater field
    $this->updateRepeaterMany($object, $fields, 'relationName', false);

    parent::afterSave($object, $fields);
}
```

### Hydrating the model for preview of revisions

```php
<?php

// implement the hydrate method
public function hydrate($object, $fields)
{
    // for exemple, to hydrate a belongToMany relationship used with the browser field
    $this->hydrateBrowser($object, $fields, 'relationName');

    // or a multiselect
    $this->hydrateMultiSelect($object, $fields, 'relationName');

    // or a repeater
    $this->hydrateRepeater($object, $fields, 'relationName');

    return parent::hydrate($object, $fields);
}
```

## Duplicating content

When enabling the duplicate feature on your [controller](./6_controllers.md) the ui will expose a button and
row action to duplicate the entry.

By default, the duplicate function will take care of duplicating the following:

- Base content and translations
- Blocks and json repeaters
- Related browsers
- Media's
- Files'

It does not duplicate related content such as regular browsers, repeaters and other relations.

This is because we cannot know how to handle these specifically for your project. But we do provide a 
method you can use to trigger these duplications, if required, after the initial duplications are complete.

To do this, you can override the `afterDuplicate` method, which receives 2 arguments. One for the old and 
one for the new model.

Do not forget to include a call to the parent if you want to have the default behaviour.

Example:

```php
use A17\Twill\Models\Contracts\TwillModelContract;

class BlogRepository extends ModuleRepository
{
    public function afterDuplicate(TwillModelContract $old, TwillModelContract $new): void
    {
        parent::afterDuplicate($old, $new);

        $new->publish_start_date = Carbon::tomorrow();
        $new->save();
    }
}
```
