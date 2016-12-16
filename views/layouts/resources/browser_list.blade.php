@foreach($items as $item)
    <a class="row_item" data-id="{{ $item->id }}" data-name="{{ $item->title or $item->id }}" href="#">
        @if($item->has('medias'))
            <div class="row_item_col thumb">
                <img src="{{ $item->cmsImage(
                    head(array_keys($item->mediasParams)),
                    head(array_keys(head($item->mediasParams))),
                    ['w' => 80, 'h' => 80]) }}" width="80" height="80">
            </div>
        @endif
        <div class="row_item_col">
            {{ $item->title }}
        </div>
    </a>
@endforeach
