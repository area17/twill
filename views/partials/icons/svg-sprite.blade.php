<div class="svg-sprite">
    @include('twill::partials.icons.icons-svg')

    @include('twill::partials.icons.icons-files-svg')

    @include('twill::partials.icons.icons-wysiwyg-svg')

    @if (\Illuminate\Support\Facades\View::exists('twill::partials.icons.icons-custom-svg'))
        @include('twill::partials.icons.icons-custom-svg')
    @endif
</div>
