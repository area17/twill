{{--

@extends('cms-toolkit::layouts.resources.form')

@section('form')
    {{ Form::model($form_fields, $form_options) }}
    @formField('publish_status')
    <section class="box">
        <header class="header_small">
            <h3><b>{{ $form_fields['title'] or 'New item' }}</b></h3>
        </header>
        @formField('input', [
            'field' => 'title',
            'field_name' => 'Title',
        ])
        @formField('textarea', [
            'field' => 'embed_video',
            'field_name' => 'Code'
        ])
        @formField('rich_textarea', [
            'field' => 'description',
            'field_name' => 'Description'
        ])
        @formField('date_picker', [
            'fieldname' => "Publish start date",
            'field' => "publish_start_date",
        ])
        @formField('select', [
            'field' => "relationship_id",
            'field_name' => "Relationship",
            'list' => $relationshipList,
            'data_behavior' => 'selector',
            'placeholder' => 'Select a relationship'
        ])
        @formField('multi_select', [
            'field' => "relationship",
            'field_name' => 'Related relationship',
            'list' => $relationshipList,
            'placeholder' => 'Add some relationship',
            'maximumSelectionLength' => 5
        ])
        @formField('checkbox', [
            'field' => 'boolean',
            'field_name' => 'Boolean?'
        ])
    </section>
    @formField('medias', ['media_role' => 'media_role'])
    @formField('files', ['file_role' => 'file role', 'file_role_name' => 'Role name'])
    @formField('block_editor', ['field_name' => 'content'])
@stop

--}}
