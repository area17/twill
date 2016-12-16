@extends('cms-toolkit::layouts.main')

@section('content')
    @yield('form')
@stop

@section('footer')
    @can('edit')
        <ul>
            <li><input type="submit" name="continue" value="Save" class="btn btn-primary"></li>
            <li><input type="submit" name="finish" value="Save and close" class="btn"></li>
            <li><a href="{{ $back_link }}" class="btn">Cancel</a></li>
        </ul>
    @endcan
    {!! Form::close() !!}
@stop
