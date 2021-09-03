<div>This is a writer</div>

<div>
    @php $titles = $item->bios->pluck('title'); @endphp

    @if ($titles->isNotEmpty())
        Bios: {{ $titles->sort()->join(', ') }}
    @else
        No bios
    @endif
</div>
