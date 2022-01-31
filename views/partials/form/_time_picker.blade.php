{{-- Obsolete with dropping laravel 6 as we use the date picker directly via the component. --}}
@formField('date_picker', [
    'label' => $label,
    'name' => $name,
    'placeholder' => $placeholder ?? null,
    'allowInput' => $allowInput ?? false,
    'allowClear' => $allowClear ?? false,
    'note' => $note ?? false,
    'required' => $required ?? false,
    'inModal' => $fieldsInModal ?? false,
    'time24Hr' => $time24Hr ?? false,
    'altFormat' => $altFormat ?? (($time24Hr ?? false) ? 'H:i' : 'h:i K'),
    'timeOnly' => true,
    'withTime' => true,
    'hourIncrement' => $hourIncrement ?? null,
    'minuteIncrement' => $minuteIncrement ?? null,
])
