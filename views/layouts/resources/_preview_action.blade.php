<td>
    <a class="icon icon-open" href="{{ $item->published ? $item->url : $item->previewUrl }}" target="_blank" title="{{ $item->published ? 'Open live site' : 'Open preview' }}">{{ $item->published ? 'Open live site' : 'Open preview' }}</a>
</td>
