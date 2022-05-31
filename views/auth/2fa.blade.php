@extends('twill::auth.layout', [
    'route' => route('twill.login-2fa'),
    'screenTitle' => twillTrans('twill::lang.auth.verify-login'),
])

@section('form')
    <fieldset class="login__fieldset">
        <label class="login__label" for="verify-code">{{ twillTrans('twill::lang.auth.otp') }}</label>
        <input type="number" name="verify-code" class="login__input" required autofocus tabindex="1"/>
    </fieldset>

    <input class="login__button" type="submit" value="{{ twillTrans('twill::lang.auth.login') }}" tabindex="3">
@stop
