@extends('twill::layouts.settings', [
    'contentFieldsetLabel' => 'Global',
    'additionalFieldsets' => [
        ['fieldset' => 'homepage', 'label' => 'Homepage'],
        ['fieldset' => 'work', 'label' => 'Work'],
        ['fieldset' => 'about', 'label' => 'About'],
        ['fieldset' => 'news', 'label' => 'News'],
        ['fieldset' => 'contact', 'label' => 'Contact'],
        ['fieldset' => 'search', 'label' => 'Search'],
    ]
])

@section('contentFields')
    @formField('input', [
        'label' => 'Global title prefix',
        'name' => 'global_title_prefix',
        'translated' => true
    ])

    @formField('input', [
        'label' => 'Global title suffix',
        'name' => 'global_title_suffix',
        'translated' => true
    ])

    @formField('input', [
        'label' => 'Global description prefix',
        'name' => 'global_description_prefix',
        'translated' => true,
    ])

    @formField('input', [
        'label' => 'Global description suffix',
        'name' => 'global_description_suffix',
        'translated' => true
    ])
@stop

@section('fieldsets')
    @formFieldset(['id' => 'homepage', 'title' => 'Homepage', 'open' => true])
        @formField('input', [
            'label' => 'Homepage title',
            'name' => 'homepage_title',
            'translated' => true
        ])

        @formField('input', [
            'label' => 'Homepage description',
            'name' => 'homepage_description',
            'translated' => true,
            'type' => 'textarea',
            'rows' => 6
        ])
    @endformFieldset

    @formFieldset(['id' => 'work', 'title' => 'Work', 'open' => true])
        @formField('input', [
            'label' => 'Work by sectors title',
            'name' => 'sectors_title',
            'translated' => true
        ])

        @formField('input', [
            'label' => 'Work by sectors description',
            'name' => 'sectors_description',
            'translated' => true,
            'type' => 'textarea',
            'rows' => 6
        ])

        @formField('input', [
            'label' => 'Work by disciplines title',
            'name' => 'disciplines_title',
            'translated' => true
        ])

        @formField('input', [
            'label' => 'Work by disciplines description',
            'name' => 'disciplines_description',
            'translated' => true,
            'type' => 'textarea',
            'rows' => 6
        ])

        @formField('input', [
            'label' => 'All works title',
            'name' => 'works_title',
            'translated' => true
        ])

        @formField('input', [
            'label' => 'All works description',
            'name' => 'works_description',
            'translated' => true,
            'type' => 'textarea',
            'rows' => 6
        ])

        @formField('input', [
            'label' => 'All works alphabetical title',
            'name' => 'works_alphabetical_title',
            'translated' => true
        ])

        @formField('input', [
            'label' => 'All works alphabetical description',
            'name' => 'works_alphabetical_description',
            'translated' => true,
            'type' => 'textarea',
            'rows' => 6
        ])

        @formField('input', [
            'label' => 'Work text mode title suffix',
            'name' => 'works_title_suffix',
            'translated' => true
        ])
    @endformFieldset

    @formFieldset(['id' => 'about', 'title' => 'About', 'open' => true])
        @formField('input', [
            'label' => 'About title',
            'name' => 'about_title',
            'translated' => true
        ])

        @formField('input', [
            'label' => 'About description',
            'name' => 'about_description',
            'translated' => true,
            'type' => 'textarea',
            'rows' => 6
        ])
    @endformFieldset

    @formFieldset(['id' => 'news', 'title' => 'News', 'open' => true])
        @formField('input', [
            'label' => 'News title',
            'name' => 'news_title',
            'translated' => true
        ])

        @formField('input', [
            'label' => 'News description',
            'name' => 'news_description',
            'translated' => true,
            'type' => 'textarea',
            'rows' => 6
        ])
    @endformFieldset

    @formFieldset(['id' => 'contact', 'title' => 'Contact', 'open' => true])
        @formField('input', [
            'label' => 'Contact title',
            'name' => 'contact_title',
            'translated' => true
        ])

        @formField('input', [
            'label' => 'Contact description',
            'name' => 'contact_description',
            'translated' => true,
            'type' => 'textarea',
            'rows' => 6
        ])
    @endformFieldset

    @formFieldset(['id' => 'search', 'title' => 'Search', 'open' => true])
        @formField('input', [
            'label' => 'Search title',
            'name' => 'search_title',
            'translated' => true
        ])

        @formField('input', [
            'label' => 'Search description',
            'name' => 'search_description',
            'translated' => true,
            'type' => 'textarea',
            'rows' => 6
        ])
    @endformFieldset
@stop
