@extends('cms-toolkit::layouts.main')

@section('appTypeClass', 'app--form')

@section('content')
    <div class="form">
        <form action="#" v-sticky data-sticky-id="navbar" data-sticky-offset="0" data-sticky-topoffset="12">
            <div class="navbar navbar--sticky" data-sticky-top="navbar">
                @php
                    $customFieldsetsItems = $customFieldsetsItems ?? [];
                    array_unshift($customFieldsetsItems, [
                        'fieldset' => 'content',
                        'label' => 'Content'
                    ]);
                @endphp
                <a17-sticky-nav data-sticky-target="navbar" :items="{{ json_encode($customFieldsetsItems) }}">
                    <a17-title-editor slot="title"></a17-title-editor>
                    <a17-langswitcher slot="actions"></a17-langswitcher>
                </a17-sticky-nav>
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
    <a17-modal class="modal--browser" ref="browser" mode="medium">
        <a17-browser />
    </a17-modal>
    <a17-overlay ref="preview" title="Preview changes">
        <a17-previewer />
    </a17-overlay>
@stop

@section('initialStore')
    window.STORE.form = {
      title: '{{ $item->title }}',
      permalink: '{{ $item->slug ?? '' }}',
      baseUrl: '{{ $baseUrl }}',
    }

    window.STORE.publication = {
      published: {{ json_encode($item->published) }},
      startDate: null,
      endDate: null
    }

    window.STORE.revisions = [
      {
        id: 1,
        author: 'George',
        datetime: '2017-09-11 16:30:10'
      },
      {
        id: 2,
        author: 'Martin',
        datetime: '2017-09-11 15:41:01'
      },
      {
        id: 3,
        author: 'George',
        datetime: '2017-09-11 11:16:45'
      },
      {
        id: 4,
        author: 'Admin',
        datetime: '2017-09-11 10:22:10'
      },
      {
        id: 5,
        author: 'Martin',
        datetime: '2017-09-11 09:30:53'
      },
      {
        id: 6,
        author: 'Martin',
        datetime: '2017-09-10 15:41:01'
      },
      {
        id: 7,
        author: 'George',
        datetime: '2017-09-09 11:16:45'
      },
      {
        id: 8,
        author: 'Admin',
        datetime: '2017-09-08 10:22:10'
      },
      {
        id: 9,
        author: 'Martin',
        datetime: '2017-09-07 09:30:53'
      }
    ]

    window.STORE.form.fields = [
      {
        name: 'event_date', // datepicker
        value: '2017-10-03 12:00'
      },
      {
        name: 'subtitle', // text-field with language
        value: {
          'fr-FR': 'FR Subtitle',
          'en-UK': 'UK Subtitle',
          'en-US': 'US subtitle',
          'de': 'de subtitle'
        }
      },
      {
        name: 'description', // text-field with language
        value: {
          'fr-FR': 'FR description',
          'en-UK': 'UK description',
          'en-US': 'US description',
          'de': 'DE description'
        }
      },
      {
        name: 'location', // location field
        value: '40.730610|-73.935242'
      },
      {
        name: 'sectors', // vselect multiple
        value: [
          {
            value: 'finance',
            label: 'Banking & Finance'
          }
        ]
      },
      {
        name: 'disciplines', // radiogroup or singleselect
        value: 'design'
      },
      {
        name: 'case_study', // wysiwyg
        value: {
          'fr-FR': '<p>FR Some html here<br />Why not it’s possible too.</p>',
          'en-UK': '<p>UK Some html here<br />Why not it’s possible too.</p>',
          'en-US': '<p>US Some html here<br />Why not it’s possible too.</p>',
          'de': '<p>DE Some html here<br />Why not it’s possible too.</p>'
        }
      }
    ]

    window.STORE.form.content = [
      {
        title: 'Title',
        icon: 'text',
        component: 'a17-block-title'
      },{
        title: 'Quote',
        icon: 'quote',
        component: 'a17-block-quote'
      },
      {
        title: 'Body text',
        icon: 'text',
        component: 'a17-block-wysiwyg'
      },
      {
        title: 'Full width Image',
        icon: 'image',
        component: 'a17-block-image',
        attributes: {
          cropContext: 'cover'
        }
      },
      {
        title: 'Grid',
        icon: 'text',
        component: 'a17-block-grid'
      },
      {
        title: 'Image Grid',
        icon: 'image',
        component: 'a17-slideshow', // example of a basic slideshow block
        attributes: {
          max: 6
        }
      }
    ]

    // example of a simple browser block to attach related content
    //
    // {
    //   title: 'Publication Grid',
    //   icon: 'text',
    //   component: 'a17-browserfield'
    //   attributes: {
    //     max: 4,
    //     itemLabel: 'Publications',
    //     endpoint: 'https://www.mocky.io/v2/59d77e61120000ce04cb1c5b',
    //     modalTitle: 'Attach publications'
    //   }
    // }

    // example of a basic full width image block
    //
    // {
    //   title: 'Full width Image',
    //   icon: 'image',
    //   component: 'a17-mediafield', // example of a basic image block
    //   attributes: {
    //     crop-context: 'cover'
    //   }
    // }

    window.STORE.form.availableRepeaters = {
      video: {
        title: 'Video',
        trigger: 'Add Videos',
        component: 'a17-block-video', // This will be project specific
        max: 4
      },
      gridItem: {
        title: 'Grid Item',
        trigger: 'Add Grid Item',
        component: 'a17-block-video', // This will be project specific
        max: 4
      }
    }

    window.STORE.medias.crops = {
      cover: {
        default: [
          {
            name: 'landscape',
            ratio: 16 / 9,
            minValues: {
              width: 1600,
              height: 900
            }
          },
          {
            name: 'portrait',
            ratio: 3 / 4,
            minValues: {
              width: 1000,
              height: 750
            }
          }
        ],
        mobile: [
          {
            name: 'mobile',
            ratio: 1,
            minValues: {
              width: 500,
              height: 500
            }
          }
        ]
      },
      listing: {
        default: [
          {
            name: 'default',
            ratio: 16 / 9,
            minValues: {
              width: 600,
              height: 284
            }
          }
        ],
        mobile: [
          {
            name: 'mobile',
            ratio: 1,
            minValues: {
              width: 300,
              height: 300
            }
          }
        ]
      },
      slideshow: {
        default: [
          {
            name: 'default',
            ratio: 16 / 9,
            minValues: {
              width: 600,
              height: 284
            }
          }
        ],
        mobile: [
          {
            name: 'mobile',
            ratio: 1,
            minValues: {
              width: 300,
              height: 300
            }
          }
        ]
      }
    }
@stop

@push('extra_js')
    <script src="{{ mix('/assets/admin/js/manifest.js') }}"></script>
    <script src="{{ mix('/assets/admin/js/vendor.js') }}"></script>
    <script src="{{ mix('/assets/admin/js/main-form.js') }}"></script>
@endpush
