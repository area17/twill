@foreach($items as $media)
    <div class="grid_item">
        <a href="#" class="thumb type-img" data-id="{{ $media->id }}" data-url="{{ route('admin.media-library.medias.edit', ['id' => $media->id]) }}" style="background-image:url({{ ImageService::getCmsUrl($media->uuid, ["h"=>"256"]) }})">
             <span class="thumb_item_text">{{ $media->filename }}</span>
        </a>
    </div>
@endforeach
