@foreach (getLocales() as $locale)
    {!! Form::hidden("active.{$locale}", 1) !!}
@endforeach
