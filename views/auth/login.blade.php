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
                        <input type="email" name="email" id="email" class="login__input" required autofocus tabindex="1" value="{{ old('email') }}" />
                    </fieldset>

                    <fieldset class="login__fieldset">
                        <label class="login__label" for="password">Password</label>
                        <a href="{{ route('admin.password.reset.link') }}" class="login__help f--small" tabindex="5"><span>Forgot password</span></a>
                        <input type="password" name="password" id="password" class="login__input" required tabindex="2" />
                    </fieldset>

                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <input class="login__button" type="submit" value="Login" tabindex="3">
                    @if (config('cms-toolkit.enabled.google-login'))
                        <a href="#" class="login__google" tabindex="4">
                            <span symbol="more-dots" class="icon icon--google-sign-in"><svg><title>Google Icon</title><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#google-sign-in"></use></svg></span>
                            <span>Sign in with Google</span>
                        </a>
                    @endif
                </form>
            </section>

            <div class="login__copyright f--small">
                <a href="https://twill.io/" target="_blank" rel="noopener">Made with
                <svg width="82" height="33" viewBox="0 0 82 33">
                    <title>Twill Logo</title>
                    <path fill="currentColor" d="M63 2h8v31h-8zM74 2h8v31h-8zM39.342 22.922L34.185 11h-5.97L23.56 21.666 18.2 11H11V5H3v6H0v7h3v15h8V18h2.025l7.902 15h5.238l5.195-11.644L36.81 33h5.154l8.053-15H52v15h8V11H45.044"/>
                    <circle fill="currentColor" cx="56" cy="5" r="4.3"/>
                </svg>
                </a>
            </div>
        </div>
    </body>
</html>
