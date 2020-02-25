@extends('twill::auth.layout', [
    'route' => route('admin.login'),
    'screenTitle' => 'Login'
])

@section('form')
    <fieldset class="login__fieldset">
        <label class="login__label" for="email">Email</label>
        <input type="email" name="email" id="email" class="login__input" required autofocus tabindex="1" value="{{ old('email') }}" />
    </fieldset>

    <fieldset class="login__fieldset">
        <label class="login__label" for="password">Password</label>
        <a href="{{ route('admin.password.reset.link') }}" class="login__help f--small" tabindex="5"><span>Forgot password</span></a>
        <input type="password" name="password" id="password" class="login__input" required tabindex="2" />
    </fieldset>

    <input class="login__button" type="submit" value="Login" tabindex="3">

    @if (config('twill.enabled.users-oauth', false))
        @foreach(config('twill.oauth.providers', []) as $index => $provider)
            <a href="{!! route('admin.login.redirect', $provider) !!}" class="login__socialite login__{{$provider}}" tabindex="{{ 4 + $index }}">
                @includeIf('twill::auth.icons.' . $provider)
                <span>Sign in with {{ ucfirst($provider)}}</span>
            </a>
        @endforeach
    @endif

@stop
