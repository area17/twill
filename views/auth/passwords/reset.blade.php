@php

$passwordText = isset($welcome) && $welcome ? 'Choose Password' : 'Reset Password';

@endphp

<!DOCTYPE html>
<html dir="ltr" lang="en-US">
    <head>
        @include('cms-toolkit::layouts.head')
    </head>
    <body>
        <div id="a17" class="login">
            <form accept-charset="UTF-8" action="{{ route('admin.password.reset') }}" class="simple_form credentials" method="post" novalidate="novalidate">
                {{ csrf_field() }}
                <input type="hidden" name="token" value="{{ $token }}">
                <section class="box box-login">
                    <header>
                        <h3><b>{{ config('app.name') }} - {{ $passwordText }}</b></h3>
                    </header>
                    @include('cms-toolkit::layouts._flash')
                    <div class="input email required credentials_email field_with_hint">
                        <label class="email required control-label" for="email">
                            Email<abbr title="required">*</abbr>
                        </label>
                        <input class="string email required" id="credentials_email" name="email" type="email" value="{{ $email }}" />
                    </div>
                    <div class="input password required credentials_password field_with_hint">
                        <label class="password required control-label" for="password">
                            Password<abbr title="required">*</abbr>
                        </label>
                        <input class="password required" id="credentials_password" name="password" type="password"/>
                    </div>
                    <div class="input password required credentials_password field_with_hint">
                        <label class="password required control-label" for="password_confirmation">
                            Confirm Password<abbr title="required">*</abbr>
                        </label>
                        <input class="password required" id="credentials_password_confirmation" name="password_confirmation" type="password"/>
                    </div>
                    <footer>
                        <input class="btn btn-small" type="submit" value="{{ $passwordText }}"/>
                    </footer>
                </section>
            </form>
        </div>
    </body>
</html>
