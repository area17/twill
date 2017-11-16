@extends('cms-toolkit::layouts.main')

@section('content')
    <div class="app app--form" id="app" v-cloak>
        <div class="form">
            <form action="#" v-sticky data-sticky-id="navbar" data-sticky-offset="0" data-sticky-topoffset="12">
                <div class="navbar navbar--sticky" data-sticky-top="navbar">
                    @php
                        array_unshift($customFieldsetsItems, [
                            'fieldset' => 'content',
                            'label' => 'Content'
                        ]);
                    @endphp
                    <a17-sticky-nav data-sticky-target="navbar" :items="{{ json_encode($customFieldsetsItems) }}"></a17-sticky-nav>
                </div>
                <div class="container">
                    <div class="wrapper wrapper--reverse" v-sticky data-sticky-id="publisher" data-sticky-offset="80">
                        <aside class="col col--aside">
                            <a17-publisher data-sticky-target="publisher"></a17-publisher>
                        </aside>
                        <section class="col col--primary">
                            <a17-fieldset title="Content" id="content" data-sticky-top="publisher">
                                @yield('contentFields')
                            </a17-fieldset>

                            @yield('fieldsets')
                        </section>
                    </div>
                </div>

                <!-- Move to trash -->
                <a17-modal class="modal--tiny modal--form modal--withintro" ref="moveToTrashModal" title="Move To Trash">
                    <p class="modal--tiny-title"><strong>Are you sure ?</strong></p>
                    <p>This change can't be undone.</p>
                    <a17-inputframe>
                        <a17-button variant="validate">Ok</a17-button> <a17-button variant="aslink" @click="$refs.moveToTrashModal.close()"><span>Cancel</span></a17-button>
                    </a17-inputframe>
                </a17-modal>
            </form>
        </div>
        <a17-modal ref="mediaLibrary" title="Media Library" mode="wide">
            <a17-medialibrary endpoint="https://www.mocky.io/v2/59edf8273300000e00b5c7d6" />
        </a17-modal>
        <a17-modal class="modal--browser" ref="browser" mode="medium">
            <a17-browser />
        </a17-modal>
        <a17-overlay ref="preview" title="Preview changes">
            <a17-previewer />
        </a17-overlay>
        <a17-notif variant="success"></a17-notif>
        <a17-notif variant="error"></a17-notif>
    </div>
@stop

@push('extra_js')
    <script src="{{ mix('/assets/admin/js/manifest.js') }}"></script>
    <script src="{{ mix('/assets/admin/js/vendor.js') }}"></script>
    <script src="{{ mix('/assets/admin/js/main-form.js') }}"></script>
@endpush
