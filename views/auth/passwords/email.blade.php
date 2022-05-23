@extends('twill::auth.layout', [
    'route' => route('twill.password.reset.email'),
    'screenTitle' => __('twill::lang.auth.reset-password')
])

@section('form')
    <fieldset class="login__fieldset">
        <label class="login__label" for="email">{{ __('twill::lang.auth.email') }}</label>
        <input type="email" name="email" id="email" class="login__input" required autofocus />
    </fieldset>

    <input class="login__button" type="submit" value="{{ __('twill::lang.auth.reset-send') }}">
@stop
