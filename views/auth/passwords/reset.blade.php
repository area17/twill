@php
$passwordText = isset($welcome) && $welcome ? 'Choose password' : 'Reset password';
@endphp

<!DOCTYPE html>
<html dir="ltr" lang="en-US">
    <head>
        @include('twill::partials.head')
    </head>
    <body class="env env--{{ app()->environment() }}">
        <div class="a17 a17--login">
            <section class="login">
                <form accept-charset="UTF-8" action="{{ route('admin.password.reset') }}" method="post">
                    <h1 class="f--heading login__heading login__heading--title">{{ config('app.name') }} <span class="envlabel envlabel--heading">{{ app()->environment() }}</span></h1>
                    <h2 class="f--heading login__heading">{{ $passwordText }}</h2>

                    <fieldset class="login__fieldset">
                        <label class="login__label" for="email">Email</label>
                        <input type="email" name="email" id="email" class="login__input" required autofocus value="{{ $email ?? '' }}" />
                    </fieldset>

                    <fieldset class="login__fieldset">
                        <label class="login__label" for="password">Password</label>
                        <input type="password" name="password" id="password" class="login__input" required />
                    </fieldset>

                    <fieldset class="login__fieldset">
                        <label class="login__label" for="password_confirmation">Confirm password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="login__input" required />
                    </fieldset>

                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="token" value="{{ $token }}">

                    <input class="login__button" type="submit" value="{{ $passwordText }}">
                </form>
            </section>
            @include('twill::partials.footer')
        </div>
    </body>
</html>
