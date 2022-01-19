<div>This is a book</div>

<div>
    @php $names = $item->getRelated('writers')->pluck('title'); @endphp

    @if ($names->isNotEmpty())
        Writers: {{ $names->sort()->join(', ') }}
    @else
        No writers
    @endif
</div>
