@extends('twill::auth.layout', [
    'route' => route(config('twill.admin_route_name_prefix') . 'login'),
    'screenTitle' => twillTrans('twill::lang.auth.login-title')
])

@section('form')
    <fieldset class="login__fieldset">
        <label class="login__label" for="email">{{ twillTrans('twill::lang.auth.email') }}</label>
        <input type="email" name="email" id="email" class="login__input" required autofocus tabindex="1" value="{{ old('email') }}" />
    </fieldset>

    <fieldset class="login__fieldset">
        <label class="login__label" for="password">{{ twillTrans('twill::lang.auth.password') }}</label>
        <a href="{{ route(config('twill.admin_route_name_prefix') . 'password.reset.link') }}" class="login__help f--small" tabindex="5"><span>{{ twillTrans('twill::lang.auth.forgot-password') }}</span></a>
        <input type="password" name="password" id="password" class="login__input" required tabindex="2" />
    </fieldset>

    <input class="login__button" type="submit" value="{{ twillTrans('twill::lang.auth.login') }}" tabindex="3">

    @if (config('twill.enabled.users-oauth', false))
        @foreach(config('twill.oauth.providers', []) as $index => $provider)
            <a href="{!! route(config('twill.admin_route_name_prefix') . 'login.redirect', $provider) !!}" class="login__socialite login__{{$provider}}" tabindex="{{ 4 + $index }}">
                @includeIf('twill::auth.icons.' . $provider)
                <span>Sign in with {{ ucfirst($provider)}}</span>
            </a>
        @endforeach
    @endif

@stop
