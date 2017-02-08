<div class="BlockButton">
    <a href="{{ $url or '#' }}" class="BlockButton__button" @if($is_file) download @endif>
        {{ $text }}
    </a>
</div>
