@foreach($images as $id => $imageByCrop)
    @php
        $image = isset($new_row) && $new_row ? $imageByCrop : $imageByCrop[key($crops)];
        $new_row_class = isset($new_row) && $new_row ? 'media-row-new' : '';
    @endphp
    <section class="box media-row {{ $new_row_class }}" id="media-box-{{ $id }}" data-id="{{ $id }}" @if(!$with_crop) style="background-color: #b2b2b2;" @endif>
        <header class="header_small">
            <h3 @if($with_multiple) data-behavior="collapse_box">
                    <i class="icon-collapse-box"></i>
                    <a href="#" class="icon-handle-on-dark"></a>
                @else
                    >
                @endif
                <b>{{ $image->alt_text }}</b>
            </h3>
            <ul>
                <li><a href="{{ ImageService::getRawUrl($image->uuid) }}" download><span class="icon icon-download"></span>Download original</a></li>
                <li><a href="#" data-media-remove-trigger><span class="icon icon-remove"></span>Detach</a></li>
            </ul>
        </header>
        @foreach($crops as $crop_name => $crop_ratio)
            @php
                $image = isset($new_row) && $new_row ? $imageByCrop : ($imageByCrop[$crop_name] ?? null);
            @endphp
            @if($image)
            <input id="medias[{{$media_role}}][{{$crop_name}}][id][]" name="medias[{{$media_role}}][{{$crop_name}}][id][]" type="hidden" value="{{ $id }}" />
            <div class="input" @if($with_crop) data-behavior="jcrop"
                                data-jcrop-js="assets/admin/vendor/jcrop/jquery.Jcrop.min"
                                data-jcrop-css="assets/admin/vendor/jcrop/jquery.Jcrop.min"
                                data-jcrop-options='{
                                    "aspectRatio":"{{$crop_ratio}}",
                                    "trueSize":[{{ $image->width }}, {{ $image->height}}],
                                    "setSelect":[{{ $image->pivot->crop_x or 0 }},{{ $image->pivot->crop_y or 0 }},{{ $image->pivot->crop_x2 or $image->width }}, {{ $image->pivot->crop_y2 or $image->height }}]
                                }' @endif>
                @if ($crop_name !== 'default')
                    <label>Cropping {{ $crop_name }}</label>
                @endif

                <img src="{{ ImageService::getCmsUrl($image->uuid, ['w' => 400]) }}" />

                @if($with_crop)
                    <input type="hidden" value="{{ $image->pivot->crop_w or '' }}" name="medias[{{$media_role}}][{{$crop_name}}][crop_w][]" id="medias[{{$media_role}}][{{$crop_name}}][crop_w][]" data-jcrop-role="w" />
                    <input type="hidden" value="{{ $image->pivot->crop_h or '' }}" name="medias[{{$media_role}}][{{$crop_name}}][crop_h][]" id="medias[{{$media_role}}][{{$crop_name}}][crop_h][]" data-jcrop-role="h" />
                    <input type="hidden" value="{{ $image->pivot->crop_x or '' }}" name="medias[{{$media_role}}][{{$crop_name}}][crop_x][]" id="medias[{{$media_role}}][{{$crop_name}}][crop_x][]" data-jcrop-role="x" />
                    <input type="hidden" value="{{ $image->pivot->crop_y or '' }}" name="medias[{{$media_role}}][{{$crop_name}}][crop_y][]" id="medias[{{$media_role}}][{{$crop_name}}][crop_y][]" data-jcrop-role="y" />
                    <input type="hidden" value="{{ $image->pivot->crop_x2 or '' }}" name="medias[{{$media_role}}][{{$crop_name}}][crop_x2][]" id="medias[{{$media_role}}][{{$crop_name}}][crop_x2][]" data-jcrop-role="x2" />
                    <input type="hidden" value="{{ $image->pivot->crop_y2 or '' }}" name="medias[{{$media_role}}][{{$crop_name}}][crop_y2][]" id="medias[{{$media_role}}][{{$crop_name}}][crop_y2][]" data-jcrop-role="y2" />
                @endif
            </div>
            @if($with_background_position)
                <div class="input text">
                    <select id="medias[{{$media_role}}][{{$crop_name}}][background_position][]" name="medias[{{$media_role}}][{{$crop_name}}][background_position][]" data-placeholder="Select a background position" data-behavior="selector" data-minimum-results-for-search=10>
                        @php
                            $available_positions = ['top', 'center', 'bottom'];
                        @endphp
                        @foreach($available_positions as $position)
                            <option value="{{ $position }}" @if($image->pivot && $image->pivot->background_position === $position) selected @endif>{{ ucfirst($position) }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            @else
                <div class="message message-error">
                    <p>A new crop is available, please reselect your media .</p>
                </div>
            @endif
        @endforeach
    </section>
@endforeach
