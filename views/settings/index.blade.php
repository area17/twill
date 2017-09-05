@extends('cms-toolkit::layouts.main')

@section('content')
    @formField('lang_switcher')
    <section class="box box-background">
        {{-- <header class="header_small">
            <h3><b>{{ ucfirst(str_replace('_', ' ', $section)) }} settings</b></h3>
        </header> --}}
        @yield('form')
    </section>
@stop

@section('footer')
    @can('edit')
        <ul>
            <li><input type="submit" class="btn btn-primary"></li>
            <li><a href="{{ Request::url() }}" class="btn">Cancel</a></li>
        </ul>
    @endcan
    {!! Form::close() !!}
@stop
