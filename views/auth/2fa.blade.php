@extends('twill::auth.layout', [
    'route' => route('admin.login-2fa'),
    'screenTitle' => 'Verify login'
])

@section('form')
    <fieldset class="login__fieldset">
        <label class="login__label" for="verify-code">One-time password</label>
        <input type="number" name="verify-code" class="login__input" required autofocus tabindex="1" />
    </fieldset>

    <input class="login__button" type="submit" value="Login" tabindex="3">
@stop
