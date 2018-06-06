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
                            <span symbol="more-dots" class="icon icon--google-sign-in">
                                <svg title="Google login" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                     width="23px" height="24px" viewBox="0 0 23 24" style="enable-background:new 0 0 23 24;" xml:space="preserve">
                                    <path fill="#4285F4" d="M21.789,10.133H12V14h5.551c-0.243,1.312-0.983,2.61-2.095,3.355l3.393,2.635
                                        c1.985-1.828,3.131-4.52,3.131-7.718C21.98,11.527,21.913,10.821,21.789,10.133z"/>
                                    <path fill="#34A853" d="M15.456,17.355c-0.94,0.63-2.143,1.002-3.556,1.002c-2.735,0-5.05-1.847-5.875-4.329l-3.508,2.72
                                        c1.728,3.432,5.279,5.785,9.383,5.785c2.835,0,5.212-0.94,6.949-2.544L15.456,17.355z"/>
                                    <path fill="#FBBC05" d="M6.025,10.038l-3.508-2.72C1.806,8.735,1.4,10.339,1.4,12.033c0,1.694,0.406,3.298,1.117,4.715l3.508-2.72
                                        c-0.21-0.63-0.329-1.303-0.329-1.995C5.695,11.341,5.815,10.668,6.025,10.038z"/>
                                    <path fill="#EA4335" d="M11.9,1.533c-4.105,0-7.655,2.353-9.383,5.785l3.508,2.72C6.85,7.556,9.165,5.709,11.9,5.709
                                        c1.542,0,2.926,0.53,4.014,1.57l3.012-3.012C17.107,2.574,14.73,1.533,11.9,1.533z"/>
                                </svg>
                            </span>
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
