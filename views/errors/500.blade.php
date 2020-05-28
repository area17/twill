@extends('twill::layouts.errors')

@section('content')
  <p>Error 500 {{ $exception->getMessage() }}</p>
  <p>Something went wrong!</p>
  <p>
    <a href="/" class="f--underlined">Homepage</a>
  </p>
@stop
