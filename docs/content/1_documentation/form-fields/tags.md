# Tags

Tags can be used to organize content and is by default available if you have enabled tags for your module.

To enable tags, add the `HandleTags` trait to your module's repository.

Form view:
```html
<x-twill::tags />
```

Form builder:
```php
Tags::make()
```

::: details Old method
@formField('tags')
:::
