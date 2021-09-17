---
pageClass: twill-doc
---

# Timepicker

```php
@formField('time_picker', [
    'name' => 'event_time',
    'label' => 'Event time',
])
```

| Option      | Description                                                  | Type/values     | Default value |
| :---------- | :----------------------------------------------------------- | :-------------- | :------------ |
| name        | Name of the field                                            | string          |               |
| label       | Label of the field                                           | string          |               |
| time24Hr    | Pick time with a 24h picker instead of AM/PM                 | true<br/>false  | false         |
| allowClear  | Adds a button to clear the field                             | true<br/>false  | false         |
| allowInput  | Allow manually editing the selected date in the field        | true<br/>false  | false         |
| hourIncrement  | Time picker hours increment        | number  | 1         |
| minuteIncrement  | Time picker minutes increment        | number  | 30         |
| altFormat   | Format used by [flatpickr](https://flatpickr.js.org/formatting/) | string          | h:i        |
| note        | Hint message displayed above the field                       | string          |               |
| required    | Displays an indicator that this field is required<br/>A backend validation rule is required to prevent users from saving | true<br/>false  | false         |


A migration to save a `time_picker` field would be:

```php
Schema::table('posts', function (Blueprint $table) {
    ...
    $table->time('event_time')->nullable();
    ...
});
// OR, if you are merging with a date field
Schema::table('posts', function (Blueprint $table) {
    ...
    $table->dateTime('event_date')->nullable();
    ...
});
```

When used in a [block](/block-editor/creating-a-block-editor.html), no migration is needed.
