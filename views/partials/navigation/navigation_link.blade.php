@if ($class === 'mobile')
    <a href="{{ $href }}" @foreach ($attributes as $attribute) {{ $attribute }} @endforeach class="@if ($is_active) s--on @endif" @if ($target_blank) target="_blank" @endif>
        {{ $title }}
    </a>
@else
    <li class="{{ $class ?? '' }} @if ($is_active) s--on @endif">
        <a href="{{ $href }}" @foreach ($attributes as $attribute) {{ $attribute }} @endforeach @if ($target_blank) target="_blank" @endif>
            {{ $title }}
        </a>
    </li>
@endif
