<!DOCTYPE html>
<html dir="ltr" lang="en-US">
    <head>
        @include('cms-toolkit::partials.head')
    </head>
    <body class="env env--{{ app()->environment() }}">
        <div class="a17 a17--login">
            <section class="login">
                <form accept-charset="UTF-8" action="{{ route('admin.login') }}" method="post">
                    <h1 class="f--heading login__heading login__heading--title">{{ config('app.name') }} <span class="envlabel envlabel--heading">{{ app()->environment() }}</span></h1>
                    <h2 class="f--heading login__heading">Login</h2>

                    <fieldset class="login__fieldset">
                        <label class="login__label" for="email">Email</label>
                        <input type="email" name="email" id="email" class="login__input" required autofocus />
                    </fieldset>

                    <fieldset class="login__fieldset">
                        <label class="login__label" for="password">Password</label>
                        <a href="{{ route('admin.password.reset.link') }}" class="login__help f--small"><span>Forgot password</span></a>
                        <input type="password" name="password" id="password" class="login__input" required />
                    </fieldset>

                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <input class="login__button" type="submit" value="Login">

                    <a href="#" class="login__google">
                        <span symbol="more-dots" class="icon icon--google-sign-in"><svg><title>Google Icon</title><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#google-sign-in"></use></svg></span>
                        <span>Sign in with Google</span>
                    </a>
                </form>
                @include('cms-toolkit::partials.footer')
            </section>
        </div>
    </body>
</html>
