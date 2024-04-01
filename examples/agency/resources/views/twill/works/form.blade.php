@extends('twill::layouts.form', [
    'additionalFieldsets' => [
        ['fieldset' => 'project_info', 'label' => 'Project info'],
        ['fieldset' => 'external_links', 'label' => 'External links'],
        ['fieldset' => 'related_content', 'label' => 'Related content'],
        ['fieldset' => 'home_settings', 'label' => 'Home settings'],
    ]
])

@section('contentFields')
    @formField('input', [
        'name' => 'subtitle',
        'label' => 'Subtitle',
        'translated' => true,
        'maxlength' => 100,
        'note' => 'Appears only on homepage slideshow and featured grid',
    ])

    @formField('input', [
        'name' => 'description',
        'label' => 'Short description',
        'translated' => true,
        'maxlength' => 150,
        'rows' => 3,
        'note' => 'Appears on case study detail page',
        'type' => 'textarea'
    ])

    @formField('wysiwyg', [
        'name' => 'case_study_text',
        'label' => 'Case study text',
        'toolbarOptions' => [
            'bold',
            'italic',
            'underline',
            'link'
        ],
        'placeholder' => '',
        'translated' => true
    ])

    @formField('medias', [
        'name' => 'cover',
        'label' => 'Cover',
        'max' => 1,
        'fieldNote' => 'Minimum image width: 2000px'
    ])

    @formField('input', [
        'name' => 'video_url',
        'label' => 'Video URL'
    ])

    @formField('checkbox', [
        'name' => 'autoplay',
        'label' => 'Autoplay'
    ])

    @formField('checkbox', [
        'name' => 'autoloop',
        'label' => 'Autoloop'
    ])

    @formField('block_editor', [
        'blocks' => ['quote', 'text', 'full-width-image']
    ])
@stop

@section('fieldsets')
    @formFieldset(['id' => 'project_info', 'title' => 'Project info', 'open' => true])
        @formField('date_picker', [
            'name' => 'publish_start_date',
            'label' => 'Publication date',
            'minDate' => '1950-09-10 12:00',
            'maxDate' => '2100-12-10 12:00'
        ])

        @formField('multi_select', [
            'name' => 'sectors',
            'label' => 'Sectors',
            'unpack' => false,
            'options' => $sectors
        ])

        @formField('multi_select', [
            'name' => 'disciplines',
            'label' => 'Disciplines',
            'unpack' => false,
            'options' => $disciplines
        ])

        @formField('input', [
            'name' => 'client_name',
            'label' => 'Client name',
        ])

        @formField('select', [
            'name' => 'year',
            'label' => 'Year',
            'placeholder' => 'Select a year',
            'options' => $years
        ])

        @formField('tags')
    @endformFieldset

    @formFieldset(['id' => 'external_links', 'title' => 'External links', 'open' => true])
        @formField('repeater', ['type' => 'external_link'])
    @endformFieldset

    @formFieldset(['id' => 'related_content', 'title' => 'Related content', 'open' => true])
        @formField('browser', [
            'moduleName' => 'people',
            'name' => 'people',
            'label' => 'Partners',
            'max' => 100,
            'endpoint' => '/admin/about/people/browser'
        ])


        @formField('browser', [
            'moduleName' => 'offices',
            'name' => 'offices',
            'label' => 'Offices',
            'max' => 100,
            'endpoint' => '/admin/contact/offices/browser'
        ])
    @endformFieldset

    @formFieldset(['id' => 'home_settings', 'title' => 'Home settings', 'open' => true])
        @formField('medias', [
            'name' => 'homepage_slideshow',
            'label' => 'Homepage slideshow',
            'max' => 1,
            'fieldNote' => 'Minimum image width: 3000px'
        ])

        @formField('files', [
            'name' => 'video',
            'label' => 'Video',
            'note' => 'Maximum file size: 8 MB'
        ])

        @formField('medias', [
            'name' => 'feature_grid',
            'label' => 'Feature grid',
            'max' => 1,
            'fieldNote' => 'Minimum image width: 2000px'
        ])
    @endformFieldset
@stop
