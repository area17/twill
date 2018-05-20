<!DOCTYPE html>
<html dir="ltr" lang="en-US">
    <head>
        @include('twill::partials.head')
    </head>
    <body class="env env--{{ app()->environment() }}">
        <div class="a17 a17--login">
            <section class="login">
                <form accept-charset="UTF-8" action="{{ route('admin.password.reset.email') }}" method="post">
                    <h1 class="f--heading login__heading login__heading--title">{{ config('app.name') }} <span class="envlabel envlabel--heading">{{ app()->environment() }}</span></h1>
                    <h2 class="f--heading login__heading">Reset password</h2>

                    <fieldset class="login__fieldset">
                        <label class="login__label" for="email">Email</label>
                        <input type="email" name="email" id="email" class="login__input" required autofocus />
                    </fieldset>

                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <input class="login__button" type="submit" value="Send password reset link">
                </form>
            </section>
            @include('twill::partials.footer')
        </div>
    </body>
</html>
