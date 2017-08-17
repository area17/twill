@if ($with_revisions ?? false)
    <section class="box @if(isset($item)) box-collapse @endif">
        <header class="header_small">
            <h3 data-behavior="collapse_box"><i class="icon-collapse-box"></i>
                <b>{{ isset($item) ? $item->revisions_count : '' }} Revision{{ isset($item) ? ($item->revisions_count > 1 ? 's' : '') : 's' }}</b>
            </h3>
            @if (isset($with_preview) && $with_preview)
                <ul>
                    <li>
                        <a data-behavior="preview" data-submit-form="{{ $form_options['id'] }}" data-options="modal_option_preview" href="#" title="Preview changes">
                            <span class="icon" style="background-repeat: no-repeat; background-image: url('https://icon.now.sh/open_in_browser/25/666'); background-position: 50%;"></span><span class="Versions__tool">Preview changes</span>
                        </a>
                    </li>
                    @if (isset($item) && isset($item->revisions_count) && $item->revisions_count > 0)
                        <li>
                            <a data-behavior="preview" data-compare data-submit-form="{{ $form_options['id'] }}" data-options="modal_option_compare" href="#" title="Compare changes">
                                <span class="icon" style="background-repeat: no-repeat; background-image: url('https://icon.now.sh/compare/25/666'); background-position: 50%;"></span><span class="Versions__tool">Compare</span>
                            </a>
                        </li>
                    @endif
                </ul>
                <script>
                    var modal_option_preview = {
                        "type": "iframe",
                        "url": "{{ $item->previewUrl }}",
                        "title": "{{ $item->previewTitle }} Preview"
                    }

                    var modal_option_compare = {
                        "type": "iframe",
                        "url": "{{ $item->previewUrl }}",
                        "title": "{{ $item->previewTitle }} Compare previews (Left: current, Right: your changes)"
                    }
                </script>
            @endif
        </header>
        <div class="Versions__panel" data-behavior="ajax_listing">
            @formField('versions_lines')
        </div>
    </section>


    @push('extra_css')
        <link href="/assets/admin/styles/versions.css" rel="stylesheet" />
    @endpush

    @push('extra_js')
        <script src="/assets/admin/behaviors/preview.js"></script>
        {{-- <script src="/assets/admin/behaviors/dirrty.js"></script> --}}
        {{-- <script src="/assets/admin/vendor/dirrty/jquery.dirrty.js"></script> --}}
        <script src="/assets/admin/behaviors/liveago.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/timeago.js/2.0.5/timeago.js"></script>
        <script>
            a17cms.Helpers.navigate_away = {
              check: function($form) {
                return $form.data('has-changed');
              },
              activate: function($form) {
                $form.data('has-changed', true);
                $form.find('div.field_with_errors').removeClass('field_with_errors');
              },
              deactivate:function($form) {
                $form.data('has-changed', false);
              },
              deactivateAll:function() {
                $forms = a17cms.getElementByBehavior("navigate_away");
                if($forms.length) a17cms.Helpers.navigate_away.deactivate($forms);
              }
            }
        </script>
    @endpush
@endif
