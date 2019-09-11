<!DOCTYPE html>
<html dir="ltr" lang="en-US">
    <head>
        @include('twill::partials.head')
    </head>
    <body class="env env--{{ app()->environment() }}">
        <div class="a17 a17--login">
            <section class="login">
                <form accept-charset="UTF-8" action="{{ $route }}" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <h1 class="f--heading login__heading login__heading--title">{{ config('app.name') }} <span class="envlabel envlabel--heading">{{ app()->environment() === 'production' ? 'prod' : app()->environment() }}</span></h1>
                    <h2 class="f--heading login__heading">{{ $screenTitle }}</h2>


                    @yield('form')
                </form>
            </section>

            @include('twill::partials.toaster')

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
