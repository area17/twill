@twillRepeaterTitle('Video')
@twillRepeaterTrigger('Add video')
@twillRepeaterGroup('app')

@formField('input', [
    'name' => 'title',
    'label' => 'Title',
    'required' => true,
    'translated' => true
])

@formField('input', [
    'name' => 'video_url',
    'label' => 'Video URL',
    'required' => true,
])

@formField('date_picker', [
    'name' => 'date',
    'label' => 'Date',
    'minDate' => '2017-09-10 12:00',
    'maxDate' => '2022-12-10 12:00'
])

@formField('medias', [
    'name' => 'video',
    'label' => 'Main',
    'max' => 1,
])
