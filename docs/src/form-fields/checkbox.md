---
pageClass: twill-doc
---

# Checkbox

![screenshot](/_media/checkbox.png)

```php
@formField('checkbox', [
    'name' => 'featured',
    'label' => 'Featured'
])
```

| Option              | Description                                             | Type            | Default value |
| :------------------ | :------------------------------------------------------ | :-------------- | :------------ |
| name                | Name of the field                                       | string          |               |
| label               | Label of the field                                      | string          |               |
| note                | Hint message displayed above the field                  | string          |               |
| default             | Sets a default value                                    | boolean         | false         |
| disabled            | Disables the field                                      | boolean         | false         | 
| requireConfirmation | Displays a confirmation dialog when modifying the field | boolean         | false         |
| confirmTitleText    | The title of the confirmation dialog                    | string          | 'Confirm selection' |
| confirmMessageText  | The text of the confirmation dialog                     | string          | 'Are you sure you want to change this option ?' |
| border              | Draws a border around the field                         | boolean         | false         |
