<div class="BlockButton">
    <div class="grid-container">
        <a href="{{ $url or '#' }}" class="BlockButton__button" @if($is_file) download @endif>
            {{ $text }}
        </a>
    </div>
</div>
