---
pageClass: twill-doc
---

# Datepicker

![screenshot](/docs/_media/datepicker.png)

```php
@formField('date_picker', [
    'name' => 'event_date',
    'label' => 'Event date',
    'minDate' => '2017-09-10 12:00',
    'maxDate' => '2017-12-10 12:00'
])
```

| Option      | Description                                                  | Type/values     | Default value |
| :---------- | :----------------------------------------------------------- | :-------------- | :------------ |
| name        | Name of the field                                            | string          |               |
| label       | Label of the field                                           | string          |               |
| minDate     | Minimum selectable date                                      | string          |               |
| maxDate     | Maximum selectable date                                      | string          |               |
| withTime    | Define if the field will display the time selector           | true<br/>false  | true          |
| time24Hr    | Pick time with a 24h picker instead of AM/PM                 | true<br/>false  | false         |
| allowClear  | Adds a button to clear the field                             | true<br/>false  | false         |
| allowInput  | Allow manually editing the selected date in the field        | true<br/>false  | false         |
| altFormat   | Format used by [flatpickr](https://flatpickr.js.org/formatting/) | string          | F j, Y        |
| hourIncrement  | Time picker hours increment        | number  | 1         |
| minuteIncrement  | Time picker minutes increment        | number  | 30         |
| note        | Hint message displayed above the field                       | string          |               |
| required    | Displays an indicator that this field is required<br/>A backend validation rule is required to prevent users from saving | true<br/>false  | false         |
| disabled            | Disables the field                                      | boolean         | false         | 


A migration to save a `date_picker` field would be:

```php
Schema::table('posts', function (Blueprint $table) {
    ...
    $table->date('event_date')->nullable();
    ...
});
// OR
Schema::table('posts', function (Blueprint $table) {
    ...
    $table->dateTime('event_date')->nullable();
    ...
});
```

When used in a [block](/block-editor/creating-a-block-editor.html), no migration is needed.
