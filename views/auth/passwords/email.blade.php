@extends('twill::auth.layout', [
    'route' => route('admin.password.reset.email'),
    'screenTitle' => __('Reset Password')
])

@section('form')
    <fieldset class="login__fieldset">
        <label class="login__label" for="email">{{ __('Email') }}</label>
        <input type="email" name="email" id="email" class="login__input" required autofocus />
    </fieldset>

    <input class="login__button" type="submit" value="{{ __('Send password reset link') }}">
@stop
