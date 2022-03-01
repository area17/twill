@extends('twill::auth.layout', [
    'route' => route('twill.login.oauth.linkProvider'),
    'screenTitle' => twillTrans('twill::lang.auth.oauth-link-title', ['provider' => ucfirst($provider)]),
]),

@section('form')
    <fieldset class="login__fieldset">
        <label class="login__label" for="email">{{ twillTrans('twill::lang.auth.email') }}</label>
        <input type="email" name="email" id="email" class="login__input" required autofocus tabindex="1" value="{{ $username }}" readonly="readonly" />
    </fieldset>

    <fieldset class="login__fieldset">
        <label class="login__label" for="password">{{ twillTrans('twill::lang.auth.password') }}</label>
        <a href="{{ route('twill.password.reset.link') }}" class="login__help f--small" tabindex="5"><span>{{ twillTrans('twill::lang.auth.login') }}</span></a>
        <input type="password" name="password" id="password" class="login__input" required tabindex="2" />
    </fieldset>

    <input class="login__button" type="submit" value="{{ twillTrans('twill::lang.auth.login') }}" tabindex="3">

    <a href="{!! route('twill.login') !!}" class="">{{ twillTrans('twill::lang.auth.back-to-login') }}</a>

@stop
