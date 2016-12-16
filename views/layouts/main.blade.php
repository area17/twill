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
                @include('cms-toolkit::layouts.navigation._global_navigation')
                @if(isset($currentUser))
                    <nav id="user-tools">
                        <ul>
                            <li><a href="{{ route('admin.logout') }}">Logout</a></li>
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
