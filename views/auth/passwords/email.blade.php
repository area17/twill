<!DOCTYPE html>
<html dir="ltr" lang="en-US">
    <head>
        @include('cms-toolkit::layouts.head')
    </head>
    <body>
        <div id="a17" class="login">
            <form accept-charset="UTF-8" action="{{ route('admin.password.reset.email') }}" class="simple_form credentials" method="post" novalidate="novalidate">
                {{ csrf_field() }}
                <section class="box box-login">
                    <header>
                        <h3><b>{{ config('app.name') }} - Reset Password</b></h3>
                    </header>
                    @include('cms-toolkit::layouts._flash')
                    <div class="input email required credentials_email field_with_hint">
                        <label class="email required control-label" for="email">
                            Email<abbr title="required">*</abbr>
                        </label>
                        <input class="string email required" id="credentials_email" name="email" type="email"/>
                    </div>
                    <footer>
                        <input class="btn btn-small" type="submit" value="Send Password Reset Link"/>
                    </footer>
                </section>
            </form>
        </div>
    </body>
</html>
