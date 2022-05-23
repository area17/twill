@extends('twill::auth.layout', [
    'route' => route('twill.login.oauth.linkProvider'),
    'screenTitle' => __('twill::lang.auth.oauth-link-title', ['provider' => ucfirst($provider)]),
]),

@section('form')
    <fieldset class="login__fieldset">
        <label class="login__label" for="email">{{ __('twill::lang.auth.email') }}</label>
        <input type="email" name="email" id="email" class="login__input" required autofocus tabindex="1" value="{{ $username }}" readonly="readonly" />
    </fieldset>

    <fieldset class="login__fieldset">
        <label class="login__label" for="password">{{ __('twill::lang.auth.password') }}</label>
        <a href="{{ route('twill.password.reset.link') }}" class="login__help f--small" tabindex="5"><span>{{ __('twill::lang.auth.login') }}</span></a>
        <input type="password" name="password" id="password" class="login__input" required tabindex="2" />
    </fieldset>

    <input class="login__button" type="submit" value="{{ __('twill::lang.auth.login') }}" tabindex="3">

    <a href="{!! route('twill.login') !!}" class="">{{ __('twill::lang.auth.back-to-login') }}</a>

@stop
