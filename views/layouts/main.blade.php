<!DOCTYPE html>
<html dir="ltr" lang="en-US">
    <head>
        @include('cms-toolkit::layouts.head')
    </head>
    <body>
        <div id="a17">
            <header id="header">
                <h1>
                    <a href="/">@if(config('cms-toolkit.custom_cms_logo', false)) @include(config('cms-toolkit.custom_cms_logo_partial')) @else {{ config('app.name') }} @endif</a>
                    <span class="env-label {{ app()->environment() }}">{{ app()->environment() }}</span>
                </h1>
                <span class="env {{ app()->environment() }}" data-behavior="env">{{ app()->environment() }}</span>
                @include('cms-toolkit::layouts.navigation._global_navigation')
                @if(isset($currentUser))
                    <nav id="user-tools">
                        <ul>
                            <li><a href="{{ route('admin.logout') }}">Logout</a></li>
                            @if (config('cms-toolkit.enabled.users-in-top-right-nav'))
                                <li @if(Route::is('admin.users.index')) class="on" @endif>
                                    <a href="{{ route('admin.users.index') }}">CMS Users</a>
                                </li>
                            @endif
                            <li>
                                <a href="{{ route('admin.users.edit', $currentUser->id) }}">
                                    {{ $currentUser->name }}
                                </a>
                            </li>
                        </ul>
                    </nav>
                @endif
                @include('cms-toolkit::layouts.navigation._primary_navigation')
            </header>
            <div id="content">
                @include('cms-toolkit::layouts.navigation._secondary_navigation')
                @include('cms-toolkit::layouts.navigation._breadcrumb')
                @include('cms-toolkit::layouts._flash')
                @yield('content')
                <footer id="footer">
                    @yield('footer')
                </footer>
            </div>
        </div>
    </body>
</html>
