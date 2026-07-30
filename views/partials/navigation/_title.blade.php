<h1 class="header__title">
    <a href={{ config('twill.enabled.dashboard') ? route(config('twill.admin_route_name_prefix') . 'dashboard') : '#' }}>
        {{ config('app.name') }}
        <span class="envlabel">
            @if(config('twill.env_label')) {{ config('twill.env_label') }} @else {{ app()->environment() === 'production' ? 'prod' : app()->environment() }} @endif
        </span>
    </a>
</h1>
