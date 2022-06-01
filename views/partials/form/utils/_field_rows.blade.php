<div class="field-rows @if(isset($columns) && $columns > 1) field-rows--{{ $columns }}cols @endif">
    @if(isset($title))
        <h3 class="field-rows__title">{{ $title }}</h3>
    @endif
    <div class="field-rows__content">
        {{ $slot }}
    </div>
</div>
