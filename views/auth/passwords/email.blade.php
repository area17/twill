@extends('twill::auth.layout', [
    'route' => route('twill.password.reset.email'),
    'screenTitle' => twillTrans('twill::lang.auth.reset-password')
])

@section('form')
    <fieldset class="login__fieldset">
        <label class="login__label" for="email">{{ twillTrans('twill::lang.auth.email') }}</label>
        <input type="email" name="email" id="email" class="login__input" required autofocus />
    </fieldset>

    <input class="login__button" type="submit" value="{{ twillTrans('twill::lang.auth.reset-send') }}">
@stop
