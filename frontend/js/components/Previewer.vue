<template>
  <div class="previewer" :class="{ 'previewer--loading' : loading }">
    <a17-button @click="restoreRevision" v-if="activeRevision" class="previewer__restore" variant="warning" size="small">Restore</a17-button>
    <a17-button @click="openEditor" v-else class="previewer__restore" variant="editor" size="small"><span v-svg symbol="editor"></span>Editor</a17-button>
    <div class="previewer__frame">
      <div class="previewer__inner">
        <div class="previewer__nav">
          <div class="previewer__revisions">
            <span class="tag tag--revision" v-if="slipScreen">Past</span>
            <a17-dropdown ref="previewRevisionsDropdown" position="bottom-left" :maxWidth="400">
              <a17-button class="previewer__trigger" @click="$refs.previewRevisionsDropdown.toggle()">
                <template v-if="activeRevision" >
                  {{ currentRevision.datetime | formatDate }} ({{ currentRevision.author }}) <span v-svg symbol="dropdown_module"></span>
                </template>
                <template v-else>
                  Last edited <timeago :auto-update="1" :since="new Date(revisions[0].datetime)"></timeago> <span v-svg symbol="dropdown_module"></span>
                </template>
              </a17-button>
              <div slot="dropdown__content">
                <button type="button" class="previewerRevision" @click="previewRevision(revision.id)" v-for="(revision, index) in revisions"  :key="revision.id">
                  <span class="previewerRevision__author">{{ revision.author }}</span>
                  <span class="previewerRevision__datetime"><span class="tag" v-if="index === 0">Current</span> {{ revision.datetime | formatDate }}</span>
                </button>
              </div>
            </a17-dropdown>
          </div>

          <ul class="previewer__breakpoints" v-if="!slipScreen">
            <li v-for="(breakpoint, index) in breakpoints" :key="breakpoint.size" class="previewer__breakpoint" :class="{ 's--active' : activeBreakpoint === breakpoint.size }" @click="resizePreview(breakpoint.size)">
              <span v-svg :symbol="breakpoint.name"></span>
            </li>
          </ul>

          <div class="previewer__compare" v-if="activeRevision">
            <a href="#" v-if="!slipScreen" @click.prevent="compareView">Compare view <span v-svg symbol="revision-compare"></span></a>
            <a href="#" v-else @click.prevent="singleView">Single view <span v-svg symbol="revision-single"></span></a>
          </div>
        </div>

        <div class="previewer__content">
          <div class="previewer__iframe">
            <a17-iframe :content="activeRevision ? activeContent : currentContent" :size="activeBreakpoint" @scrollDoc="setIframeScroll" :scrollPosition="scrollPosition"></a17-iframe>
          </div>
          <div class="previewer__iframe" v-if="slipScreen">
            <div class="previewer__iframeInfos"><span class="tag tag--revision">Current</span>{{ revisions[0].datetime  | formatDate }} ({{ revisions[0].author }})</div>
            <a17-iframe :content="currentContent" @scrollDoc="setIframeScroll" :scrollPosition="scrollPosition"></a17-iframe>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  // nb : UI is quite similar to https://github.com/nerijusgood/viewport-resizer

  import { mapState } from 'vuex'
  import A17PreviewerFrame from '@/components/PreviewerFrame.vue'
  import a17VueFilters from '@/utils/filters.js'

  export default {
    name: 'A17previewer',
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
        loading: state => state.revision.loading,
        currentRevision: state => state.revision.active,
        activeContent: state => state.revision.activeContent,
        currentContent: state => state.revision.currentContent,
        revisions: state => state.revision.all
      })
    },
    methods: {
      openEditor: function () {
        this.$root.$refs.preview.close()
        if (this.$root.$refs.editor) this.$root.$refs.editor.open()
      },
      getCurrentPreview: function () {
        if (this.loadedCurrent) return

        this.loadedCurrent = true
        this.$store.dispatch('getCurrentContent')
      },
      restoreRevision: function () {
        // Do something here
      },
      resizePreview: function (size) {
        this.activeBreakpoint = parseInt(size)
        this.lastActiveBreakpoint = parseInt(size)
      },
      previewRevision: function (id) {
        this.$store.commit('updateRevision', id)

        // Update preview HTML
        this.$store.dispatch('getRevisionContent')
      },
      compareView: function () {
        this.activeBreakpoint = 0
        this.slipScreen = true

        if (this.activeRevision) this.getCurrentPreview()
      },
      singleView: function () {
        this.activeBreakpoint = this.lastActiveBreakpoint
        this.slipScreen = false
      },
      setIframeScroll: function (value) {
        this.scrollPosition = value
      }
    },
    mounted: function () {
      // get preview HTML
      if (this.activeRevision) this.$store.dispatch('getRevisionContent')
      else this.getCurrentPreview()
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

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
      text-decoration:none;
    }
  }

  .previewer__compare .icon {
    position:relative;
    margin-left:9px;
    top:2px;
  }

  .previewer__revisions,
  .previewer__compare {
    margin-left:20px;
    margin-right:20px;
    padding-top: 40px;
  }

  .previewer__revisions {
    padding-top: 40px;
    flex-grow:1;
    position:relative;
  }

  .previewer__breakpoints {
    margin: 0 auto;
    position:absolute;
    top: 0;
    left: 50%;
    font-size:0;
    transform:translateX(-50%);
    height:$height__nav;
    line-height:$height__nav;
  }

  .previewer__breakpoint {
    cursor:pointer;
    display:inline-block;
    color:$color__text--light;
    padding:25px 15px;
    vertical-align: bottom;

    .icon {
      display:block;
    }

    &.s--active {
      color:$color__background;
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

  .previewerRevision__author {
    padding-right:30px;
    flex-grow: 1;
  }

  .previewerRevision__datetime {
    color:$color__link;
  }
</style>
