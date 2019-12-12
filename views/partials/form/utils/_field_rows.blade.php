<div class="field-rows">
    @if(isset($title))
        <h3 class="field-rows__title">{{ $title }}</h3>
    @endif
    <div class="field-rows__content">
        {{ $slot }}
    </div>
</div>
