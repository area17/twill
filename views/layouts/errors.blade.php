<!DOCTYPE html>
<html dir="ltr" lang="en-US">
    <head>
        @include('cms-toolkit::layouts.head')
    </head>
    <body>
        <div id="a17">
            <header id="header">
                <h1>
                    <a href="/">{{ config('app.name') }}</a>
                    <span class="env-label {{ app()->environment() }}">{{ app()->environment() }}</span>
                </h1>
                <span class="env {{ app()->environment() }}" data-behavior="env">{{ app()->environment() }}</span>
            </header>
            <div id="content">
                @yield('content')
            </div>
        </div>
    </body>
</html>
