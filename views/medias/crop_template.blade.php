@extends('cms-toolkit::layouts.modal')

@push('extra_js')
    <script src="/assets/admin/behaviors/crop_media_modal.js"></script>
@endpush

@section('content')
    <div class="grid-frame">
        <div class="grid" data-behavior="crop_media_modal">
            <input id="media_id" name="media_id" type="hidden" value="{{ $media->id }}" />
            <div style="display:table; width:100%; height:100%; vertical-align: middle">
                <div style="display:table-cell; width:100%; height:100%; text-align: center; vertical-align: middle">
                    <div class="input"  data-behavior="jcrop"
                                data-jcrop-js="assets/admin/vendor/jcrop/jquery.Jcrop.min"
                                data-jcrop-css="assets/admin/vendor/jcrop/jquery.Jcrop.min"
                                data-jcrop-options='{
                                    "aspectRatio":"{{ $blockRatio or '16/9' }}",
                                    "trueSize":[{{ $media->width }}, {{ $media->height}}],
                                    "setSelect":[{{ $crop['crop_x'] or 0 }},{{ $crop['crop_y'] or 0 }},{{ $crop['crop_x2'] or $media->width }}, {{ $crop['crop_y2'] or $media->height }}]
                                }' style="display:inline-block; margin:0 auto; text-align: center;">

                        <img src="{{ ImageService::getCmsUrl($media->uuid, ['h' => 450]) }}"/>

                        <input type="hidden" value="" name="crop_w" id="crop_w" data-jcrop-role="w" />
                        <input type="hidden" value="" name="crop_h" id="crop_h" data-jcrop-role="h" />
                        <input type="hidden" value="" name="crop_x" id="crop_x" data-jcrop-role="x" />
                        <input type="hidden" value="" name="crop_y" id="crop_y" data-jcrop-role="y" />
                        <input type="hidden" value="" name="crop_x2" id="crop_x2" data-jcrop-role="x2" />
                        <input type="hidden" value="" name="crop_y2" id="crop_y2" data-jcrop-role="y2" />
                    </div>
                </div>
            </div>
            <footer class="grid_footer">
                <button class="btn btn-primary" data-crop-insert type="button">Crop</button>
                <button class="btn" data-behavior="close_parent_modal" type="button">Cancel</button>
            </footer>
        </div>
    </div>
@stop
