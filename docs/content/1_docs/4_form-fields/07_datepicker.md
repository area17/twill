# Datepicker

## Datepicker

![screenshot](/assets/datepicker.png)

:::tabs=currenttab.FormBuilder&items.FormBuilder|FormView|Directive:::
:::tab=name.FormBuilder:::

```php
DatePicker::make()
    ->name('event_date')
```

:::#tab:::
:::tab=name.FormView:::

```blade
<x-twill::date-picker 
    name="event_date" 
    label="Event date"
    :minDate="\Carbon\Carbon::now()->format('Y-m-d H:i)"
    maxDate="2030-01-01 12:00"
/>
```

:::#tab:::
:::tab=name.Directive:::

```blade
@formField('date_picker', [
    'name' => 'event_date',
    'label' => 'Event date',
    'minDate' => '2017-09-10 12:00',
    'maxDate' => '2017-12-10 12:00'
])
```

:::#tab:::
:::#tabs:::

| Option          | Description                                                                                                              | Type/values | Default value |
|:----------------|:-------------------------------------------------------------------------------------------------------------------------|:------------|:--------------|
| name            | Name of the field                                                                                                        | string      |               |
| label           | Label of the field                                                                                                       | string      |               |
| minDate         | Minimum selectable date                                                                                                  | string      |               |
| maxDate         | Maximum selectable date                                                                                                  | string      |               |
| withTime        | Define if the field will display the time selector                                                                       | boolean     | true          |
| time24Hr        | Pick time with a 24h picker instead of AM/PM                                                                             | boolean     | false         |
| allowClear      | Adds a button to clear the field                                                                                         | boolean     | false         |
| allowInput      | Allow manually editing the selected date in the field                                                                    | boolean     | false         |
| altFormat       | Format used by [flatpickr](https://flatpickr.js.org/formatting/)                                                         | string      | F j, Y        |
| hourIncrement   | Time picker hours increment                                                                                              | number      | 1             |
| minuteIncrement | Time picker minutes increment                                                                                            | number      | 30            |
| note            | Hint message displayed above the field                                                                                   | string      |               |
| required        | Displays an indicator that this field is required<br/>A backend validation rule is required to prevent users from saving | boolean     | false         |
| disabled        | Disables the field                                                                                                       | boolean     | false         | 

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

When used in a [block](../5_block-editor/02_creating-a-block-editor.md), no migration is needed.

## Timepicker

:::tabs=currenttab.FormBuilder&items.FormBuilder|FormView|Directive:::
:::tab=name.FormBuilder:::

```php
// TODO
```

:::#tab:::
:::tab=name.FormView:::

```blade
<x-twill::time-picker 
    name="event_time" 
    label="Event time"
/>
```

:::#tab:::
:::tab=name.Directive:::

```blade
@formField('time_picker', [
    'name' => 'event_time',
    'label' => 'Event time',
])
```

:::#tab:::
:::#tabs:::

| Option          | Description                                                                                                              | Type/values | Default value |
|:----------------|:-------------------------------------------------------------------------------------------------------------------------|:------------|:--------------|
| name            | Name of the field                                                                                                        | string      |               |
| label           | Label of the field                                                                                                       | string      |               |
| time24Hr        | Pick time with a 24h picker instead of AM/PM                                                                             | boolean     | false         |
| allowClear      | Adds a button to clear the field                                                                                         | boolean     | false         |
| allowInput      | Allow manually editing the selected date in the field                                                                    | boolean     | false         |
| hourIncrement   | Time picker hours increment                                                                                              | number      | 1             |
| minuteIncrement | Time picker minutes increment                                                                                            | number      | 30            |
| altFormat       | Format used by [flatpickr](https://flatpickr.js.org/formatting/)                                                         | string      | h:i           |
| note            | Hint message displayed above the field                                                                                   | string      |               |
| required        | Displays an indicator that this field is required<br/>A backend validation rule is required to prevent users from saving | boolean     | false         |

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

In addition to that you need to setup `$dates => ['event_date']` in your module.

When used in a [block](../5_block-editor), no migration is needed.
