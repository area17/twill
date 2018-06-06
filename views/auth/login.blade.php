<!DOCTYPE html>
<html dir="ltr" lang="en-US">
    <head>
        @include('twill::partials.head')
    </head>
    <body class="env env--{{ app()->environment() }}">
        <div class="a17 a17--login">
            <section class="login">
                <form accept-charset="UTF-8" action="{{ route('admin.login') }}" method="post">
                    <h1 class="f--heading login__heading login__heading--title">{{ config('app.name') }} <span class="envlabel envlabel--heading">{{ app()->environment() === 'production' ? 'prod' : app()->environment() }}</span></h1>
                    <h2 class="f--heading login__heading">Login</h2>

                    <fieldset class="login__fieldset">
                        <label class="login__label" for="email">Email</label>
                        <input type="email" name="email" id="email" class="login__input" required autofocus tabindex="1" value="{{ old('email') }}" />
                    </fieldset>

                    <fieldset class="login__fieldset">
                        <label class="login__label" for="password">Password</label>
                        <a href="{{ route('admin.password.reset.link') }}" class="login__help f--small" tabindex="5"><span>Forgot password</span></a>
                        <input type="password" name="password" id="password" class="login__input" required tabindex="2" />
                    </fieldset>

                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <input class="login__button" type="submit" value="Login" tabindex="3">
                    @if (config('twill.enabled.google-login'))
                        <a href="#" class="login__google" tabindex="4">
                            <span symbol="more-dots" class="icon icon--google-sign-in"><svg><title>Google Icon</title><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#google-sign-in"></use></svg></span>
                            <span>Sign in with Google</span>
                        </a>
                    @endif
                </form>
            </section>

            <div class="login__copyright f--small">
                <a href="https://twill.io/" target="_blank" rel="noopener">Made with
                    <svg xmlns="http://www.w3.org/2000/svg" width="55" height="24">
                        <path fill="currentColor" d="M42 2h5v21h-5zM49 2h5v21h-5zM26.776 16.587L23.24 9h-4.097l-3.37 7.11L12.532 9H8V5H3v4H1v4h2v10h5V13h1.333l5.205 10h3.449l3.421-7.762L24.998 23h3.393l5.303-10H35v10h5V9h-9.66z"/>
                        <circle fill="currentColor" cx="37.5" cy="4.5" r="2.875"/>
                    </svg>
                </a>
            </div>
        </div>
    </body>
</html>
