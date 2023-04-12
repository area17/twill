# Form Requests

Classic Laravel 5+ [form request validation](https://laravel.com/docs/10.x/validation#form-request-validation).

Once you generated the module using Twill's CLI module generator, it will also prepare the `App/Http/Requests/Twill/ModuleNameRequest.php` for you to use.
You can choose to use different rules for creation and update by implementing the following 2 functions instead of the classic `rules` one:

```php
<?php

public function rulesForCreate()
{
    return [];
}

public function rulesForUpdate()
{
    return [];
}
```

There is also an helper to define rules for translated fields without having to deal with each locales:

```php
<?php

$this->rulesForTranslatedFields([
 // regular rules
], [
  // translated fields rules with just the field name like regular rules
]);
```

There is also an helper to define validation messages for translated fields:

```php
<?php

$this->messagesForTranslatedFields([
 // regular messages
], [
  // translated fields messages
]);
```

Once you defined the rules in this file, the UI will show the corresponding validation error state or message next to the corresponding form field.

## Validating repeater fields

To validate repeater fields added to your model you can reuse the same `rulesForCreate` and `rulesForUpdate` methods.

If your repeater is named `accordion_item` and you want to add validation to the `headline` field you can use:

```php
public function rulesForCreate()
{
    return ['repeaters.accordion_item.*.header' => 'required'];
}
```

Alternatively if your field is translatable you can use the helpers as defined above:

```php
public function rulesForUpdate()
{
    return $this->rulesForTranslatedFields([], [
        'repeaters.accordion_item.*.header' => 'required'
    ]);
}
```
