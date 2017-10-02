@extends('cms-toolkit::layouts.main')

@php
    $hide_lang_switcher = $hide_lang_switcher ?? true;
    $with_view_link = $with_view_link ?? false;
@endphp

@section('content')
    @formField('lang_switcher', ['hidden' => $hide_lang_switcher])
    @yield('form')
@stop

@section('footer')
    @can('edit')
        <ul>
            <li><input type="submit" name="continue" value="Save" class="btn btn-primary"></li>
            <li><input type="submit" name="finish" value="Save and close" class="btn"></li>
            <li><a href="{{ $back_link }}" class="btn">Cancel</a></li>
            @if ($with_view_link)
                <li class="float-right"><a class="btn" target="_blank" href="{{ $item->url }}">Open live site</a></li>
            @endif
        </ul>
    @endcan
    {!! Form::close() !!}
@stop
