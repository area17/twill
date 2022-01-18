---
pageClass: twill-doc
---

# Form Requests

Classic Laravel 5 [form request validation](https://laravel.com/docs/5.5/validation#form-request-validation).

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
