@php
$passwordText = isset($welcome) && $welcome ? twillTrans('twill::lang.auth.choose-password') : twillTrans('twill::lang.auth.reset-password');
@endphp

@extends('twill::auth.layout', [
    'route' => route('twill.password.reset'),
    'screenTitle' => $passwordText
])

@section('form')
    <fieldset class="login__fieldset">
        <label class="login__label" for="email">{{ twillTrans('twill::lang.auth.email') }}</label>
        <input type="email" name="email" id="email" class="login__input" required autofocus value="{{ $email ?? '' }}" />
    </fieldset>

    <fieldset class="login__fieldset">
        <label class="login__label" for="password">{{ twillTrans('twill::lang.auth.password') }}</label>
        <input type="password" name="password" id="password" class="login__input" required />
    </fieldset>

    <fieldset class="login__fieldset">
        <label class="login__label" for="password_confirmation">{{ twillTrans('twill::lang.auth.password-confirmation') }}</label>
        <input type="password" name="password_confirmation" id="password_confirmation" class="login__input" required />
    </fieldset>

    <input type="hidden" name="token" value="{{ $token }}">

    <input class="login__button" type="submit" value="{{ $passwordText }}">
@stop
