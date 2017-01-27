@foreach($items as $id => $item)

    <tr class="media-row media-row-new" id="media-box-{{ $item->id }}" data-id="{{ $item->id }}">
        @if($with_multiple)
            <td><span class="icon icon-handle"></span></td>
        @endif
        <td class="thumb">
            @if($item->has('medias'))
                <img src="{{ $item->cmsImage(
                    head(array_keys($item->mediasParams)),
                    head(array_keys(head($item->mediasParams))),
                    ['w' => 80, 'h' => 80]) }}" width="80" height="80">
            @endif
        </td>
      <td>{{$item->title}}</td>
      <td><a class="icon icon-trash" href="#" data-media-remove-trigger rel="nofollow">Destroy</a></td>
    </tr>

@endforeach
