# Tags

Tags can be used to organize content and is by default available if you have enabled tags for your module.

To enable tags, add the `HandleTags` trait to your module's repository.

:::tabs=currenttab.FormBuilder&items.FormBuilder|FormView|Directive:::
:::tab=name.FormBuilder:::

```php
Tags::make()
```

:::#tab:::
:::tab=name.FormView:::

```blade
<x-twill::tags />
```

:::#tab:::
:::tab=name.Directive:::

```blade
@formField('tags')
```

:::#tab:::
:::#tabs:::
