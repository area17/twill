<h1 class="header__title">
    <a href={{ config('twill.enabled.dashboard') ? route(config('twill.admin_route_name_prefix') . 'dashboard') : '#' }}>
        {{ config('app.name') }}
        <span class="envlabel">
            {{ app()->environment() === 'production' ? 'prod' : app()->environment() }}
        </span>
    </a>
</h1>
