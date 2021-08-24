<template>
  <a17-overlay ref="overlay" :title="$trans('previewer.title')">
  <div class="previewer" :class="{ 'previewer--loading' : loading }" v-if="revisions.length">
    <a17-button @click="restoreRevision" v-if="activeRevision" class="previewer__restore" variant="warning" size="small">{{ $trans('previewer.restore') }}</a17-button>
    <a17-button @click="openEditor" v-if="!activeRevision && editor" class="previewer__restore" variant="editor" size="small"><span v-svg symbol="editor" class="hide--xsmall"></span>{{ $trans('previewer.editor') }}</a17-button>
    <div class="previewer__frame">
      <div class="previewer__inner">
        <div class="previewer__nav">
          <div class="previewer__revisions">
            <span class="tag tag--revision" v-if="slipScreen">{{ $trans('previewer.past-revision') }}</span>
            <a17-dropdown ref="previewRevisionsDropdown" position="bottom-left" :maxWidth="400" :maxHeight="300">
              <a17-button class="previewer__trigger" @click="$refs.previewRevisionsDropdown.toggle()">
                <template v-if="activeRevision" >
                  {{ currentRevision.datetime | formatDate }} ({{ currentRevision.author }}) <span v-svg symbol="dropdown_module"></span>
                </template>
                <template v-else>
                  {{ $trans('previewer.last-edit') }} <timeago :auto-update="1" :datetime="new Date(revisions[0].datetime)"></timeago> <span v-svg symbol="dropdown_module"></span>
                </template>
              </a17-button>
              <div slot="dropdown__content">
                <button type="button" class="previewerRevision" :class="{ 'previewerRevision--active' : currentRevision.id === revision.id }" @click="toggleRevision(revision.id)" v-for="(revision, index) in revisions"  :key="revision.id">
                  <span class="previewerRevision__author">{{ revision.author }}</span>
                  <span class="previewerRevision__datetime"><span class="tag" v-if="index === 0">{{ $trans('previewer.current-revision') }}</span> {{ revision.datetime | formatDate }}</span>
                </button>
              </div>
            </a17-dropdown>
          </div>

          <ul class="previewer__breakpoints" v-if="!slipScreen">
            <li v-for="breakpoint in breakpoints" :key="breakpoint.size" class="previewer__breakpoint" :class="{ 's--active' : activeBreakpoint === breakpoint.size }">
              <a href="#" @click.prevent="resizePreview(breakpoint.size)">
                <span v-svg :symbol="breakpoint.name"></span>
              </a>
            </li>
          </ul>

          <div class="previewer__compare" v-if="activeRevision">
            <a href="#" v-if="!slipScreen" @click.prevent="compareView"><span class="previewer__compareLabel">{{ $trans('previewer.compare-view') }}</span> <span v-svg symbol="revision-compare"></span></a>
            <a href="#" v-else @click.prevent="singleView"><span class="previewer__compareLabel">{{ $trans('previewer.single-view') }}</span> <span v-svg symbol="revision-single"></span></a>
          </div>
        </div>

        <div class="previewer__content">
          <div class="previewer__iframe">
            <a17-iframe :content="activeRevision ? activeContent : currentContent" :size="activeBreakpoint" @scrollDoc="setIframeScroll" :scrollPosition="scrollPosition"></a17-iframe>
          </div>
          <div class="previewer__iframe" v-if="slipScreen">
            <div class="previewer__iframeInfos"><span class="tag tag--revision">{{ $trans('previewer.current-revision') }}</span>{{ $trans('previewer.unsaved') }}</div>
            <a17-iframe :content="currentContent" @scrollDoc="setIframeScroll" :scrollPosition="scrollPosition"></a17-iframe>
          </div>
        </div>
      </div>
    </div>
  </div>
  </a17-overlay>
</template>

<script>
// nb : UI is quite similar to https://github.com/nerijusgood/viewport-resizer

  import { mapState } from 'vuex'

  import { REVISION, FORM, NOTIFICATION } from '@/store/mutations'
  import ACTIONS from '@/store/actions'

  import A17PreviewerFrame from '@/components/PreviewerFrame.vue'
  import a17VueFilters from '@/utils/filters.js'

  export default {
    name: 'A17Previewer',
    components: {
      'a17-iframe': A17PreviewerFrame
    },
    data: function () {
      return {
        loadedCurrent: false,
        slipScreen: false,
        activeBreakpoint: 1280,
        lastActiveBreakpoint: 1280,
        scrollPosition: 0,
        breakpoints: [
          {
            size: 1280,
            name: 'preview-desktop'
          },
          {
            size: 1024,
            name: 'preview-tablet-h'
          },
          {
            size: 768,
            name: 'preview-tablet-v'
          },
          {
            size: 320,
            name: 'preview-mobile'
          }
        ]
      }
    },
    filters: a17VueFilters,
    computed: {
      activeRevision: function () {
        return Object.keys(this.currentRevision).length
      },
      ...mapState({
        editor: state => state.blocks.editor,
        loading: state => state.revision.loading,
        currentRevision: state => state.revision.active,
        activeContent: state => state.revision.activeContent,
        currentContent: state => state.revision.currentContent,
        revisions: state => state.revision.all,
        restoreRevisionUrl: state => state.form.restoreUrl
      })
    },
    methods: {
      open: function (previewId = 0) {
        const self = this

        // reset previewer state
        this.loadedCurrent = false
        this.activeBreakpoint = 1280
        this.lastActiveBreakpoint = 1280

        function initPreview () {
          if (self.$refs.overlay) self.$refs.overlay.open()
          self.singleView()
        }

        if (previewId) this.previewRevision(previewId, function () { initPreview() })
        else this.previewCurrent(function () { initPreview() })
      },
      close: function () {
        this.$refs.overlay.close()
      },
      openEditor: function () {
        const rootRefs = this.$root.$refs
        if (rootRefs.preview) rootRefs.preview.close()
        if (rootRefs.editor) rootRefs.editor.open()
      },
      restoreRevision: function () {
        window.location.href = this.restoreRevisionUrl + '?revisionId=' + this.currentRevision.id
      },
      resizePreview: function (size) {
        this.activeBreakpoint = parseInt(size)
        this.lastActiveBreakpoint = parseInt(size)
      },
      previewCurrent: function (callback) {
        this.$store.commit(REVISION.UPDATE_REV, 0)
        this.loadCurrent(callback)
      },
      loadCurrent: function (callback) {
        if (this.loadedCurrent) {
          if (callback && typeof callback === 'function') callback()
          return
        }

        this.loadedCurrent = true
        this.$store.dispatch(ACTIONS.GET_CURRENT).then(() => {
          if (callback && typeof callback === 'function') callback()
        }, (errorResponse) => {
          this.$store.commit(FORM.SET_FORM_ERRORS, errorResponse.response.data)
          this.$store.commit(NOTIFICATION.SET_NOTIF, {
            message: 'Your submission could not be validated, please fix and retry',
            variant: 'error'
          })
        })
      },
      toggleRevision: function (id) {
        if (this.activeRevision) {
          // Toggle : go back to current version in Single view mode
          if (this.currentRevision.id === id) {
            this.singleView()
            this.previewCurrent()
            return
          }
        }

        // Or display the revision
        this.previewRevision(id)
      },
      previewRevision: function (id, callback) {
        this.$store.commit(REVISION.UPDATE_REV, id)
        this.$store.dispatch(ACTIONS.GET_REVISION).then(() => {
          if (callback && typeof callback === 'function') callback()
        }, (errorResponse) => {
          this.$store.commit(NOTIFICATION.SET_NOTIF, {
            message: 'Invalid revision.',
            variant: 'error'
          })
        })
      },
      compareView: function () {
        this.activeBreakpoint = 0
        this.slipScreen = true

        if (this.activeRevision) this.loadCurrent()
      },
      singleView: function () {
        this.activeBreakpoint = this.lastActiveBreakpoint
        this.slipScreen = false
      },
      setIframeScroll: function (value) {
        this.scrollPosition = value
      }
    }
  }
</script>

<style lang="scss" scoped>

  $height__nav: 80px;

  .previewer {
    display: block;
    width: 100%;
    padding: 0;
    position:relative;
    flex-grow:1;
    background-color:$color__overlay--background;
  }

  .previewer__restore {
    position:fixed;
    right:20px;
    top:13px;
    z-index:$zindex__overlay + 1;
  }

  .tag--revision {
    color:$color__text;
    position:absolute;
    top: 17px;
    left: 0;
    margin: 0;
    opacity:0.5;
  }

  .previewer__nav {
    display: flex;
    flex-direction: row;
    height:$height__nav;
    opacity:1;
    transition: opacity .3s ease;
  }

  .previewer__frame {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display:flex;
    flex-flow: column nowrap;
  }

  .previewer__inner {
    position: relative;
    width: 100%;
    overflow: hidden;
    flex-grow: 1;
    display:flex;
    flex-flow: column nowrap;
  }

  .previewer__trigger {
    height:auto;
    line-height: inherit;

    .icon {
      margin-left:6px;
    }
  }

  .previewer__trigger,
  .previewer__compare {
    color:$color__text--light;
    padding-left:0;
    padding-right:0;

    &:hover,
    &:focus {
      color:$color__background;
    }

    a {
      white-space: nowrap;
      overflow: hidden;
      text-decoration:none;
    }
  }

  .previewer__compare {
    @include breakpoint('medium+') {
      margin-left:20px;
    }

    .icon {
      position:relative;
      margin-left:9px;
      top:2px;
    }
  }

  .previewer__compareLabel {
    display:none;

    @include breakpoint('small+') {
      display:inline;
    }
  }

  .previewer__revisions,
  .previewer__compare {
    margin-right:20px;
    padding-top: 40px;
  }

  .previewer__revisions {
    margin-left:20px;
    padding-top: 40px;
    flex-grow:1;
    position:relative;
  }

  .previewer__breakpoints {
    display:none;

    @include breakpoint('medium+') {
      display:block;
      margin: 0 auto;
      position:absolute;
      top: 0;
      left: 50%;
      font-size:0;
      transform:translateX(-50%);
      height:$height__nav;
      line-height:$height__nav;
    }
  }

  .previewer__breakpoint {
    display:inline-block;
    color:$color__text--light;
    padding:25px 15px;
    vertical-align: bottom;

    a {
      display:block;

      &:hover,
      &:focus {
        color:$color__icons;
      }
    }

    .icon {
      display:block;
    }

    &.s--active {
      color:$color__background;

      a {
        &:hover,
        &:focus {
          color:$color__background;
        }
      }
    }
  }

  .previewer__content {
    width: 100%;
    height: 100%;
    display:flex;
    flex-grow:1;
    flex-flow: row nowrap;
  }

  .previewer__iframe {
    width: 100%;
    opacity:1;
    transition: opacity .3s ease, width .3s ease;
    position: relative;
    display: flex;
    flex-grow: 1;
  }

  .previewer--loading {
    .previewer__nav,
    .previewer__iframe {
      opacity:0;
      pointer-events: none;
    }

    .previewer__content {
      &::after {
        content: 'Loading preview...';
        position:absolute;
        top:25%;
        left:50%;
        width:200px;
        margin-left:-100px;
        text-align:center;
        color:$color__text--light;
      }
    }
  }

  .previewer__iframeInfos {
    height:80px;
    margin-top:-80px;
    position:absolute;
    color:$color__text--light;
    top:0;
    left:10px;
    padding-top:40px
  }

  button.previewerRevision {
    display:flex;
    padding:0 15px;
  }

  button.previewerRevision--active {
    color:$color__text;
    background:$color__light;
  }

  .previewerRevision__author {
    padding-right:10px;
    flex-grow: 1;
    white-space: nowrap;

    @include breakpoint('small+') {
      padding-right:30px;
    }
  }

  .previewerRevision__datetime {
    color:$color__link;
    white-space: nowrap;
    overflow:hidden;
    text-overflow: ellipsis;
  }
</style>
