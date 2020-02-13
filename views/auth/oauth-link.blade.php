@extends('twill::auth.layout', [
    'route' => route('admin.login.oauth.linkProvider'),
    'screenTitle' => "Re-enter your password to link " . ucfirst($provider) . " to your account"
])

@section('form')
    <fieldset class="login__fieldset">
        <label class="login__label" for="email">Email</label>
        <input type="email" name="email" id="email" class="login__input" required autofocus tabindex="1" value="{{ $username }}" readonly="readonly" />
    </fieldset>

    <fieldset class="login__fieldset">
        <label class="login__label" for="password">Password</label>
        <a href="{{ route('admin.password.reset.link') }}" class="login__help f--small" tabindex="5"><span>Forgot password</span></a>
        <input type="password" name="password" id="password" class="login__input" required tabindex="2" />
    </fieldset>

    <input class="login__button" type="submit" value="Login" tabindex="3">

    <a href="{!! route('admin.login') !!}" class="">Back to Login</a>

@stop
