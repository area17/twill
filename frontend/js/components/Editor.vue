<template>
  <a17-overlay ref="overlay" title="Content editor" @close="closeEditor" @open="openEditor">
    <div class="editor">
      <a17-button class="editor__leave" variant="editor" size="small" @click="openPreview" v-if="revisions.length"><span v-svg symbol="preview" class="hide--xsmall"></span>Preview</a17-button>
      <div class="editor__frame">
        <div class="editor__inner">
          <div class="editor__sidebar" :class="sidebarClass" ref="sidebar">
            <a17-editorsidebar @delete="deleteBlock" @save="saveBlock" @cancel="cancelBlock">Add content</a17-editorsidebar>
          </div>
          <div class="editor__resizer" @mousedown="resize"><span></span></div>
          <div class="editor__preview" :class="previewClass" :style="previewStyle">
            <a17-editorpreview ref="previews" @select="selectBlock" @delete="deleteBlock" @unselect="unselectBlock" @add="addBlock" />
            <a17-spinner v-if="loading" :visible="true">Loading&hellip;</a17-spinner>
          </div>
        </div>
      </div>
    </div>
  </a17-overlay>
</template>

<script>
  import tinyColor from 'tinycolor2'
  import { mapState } from 'vuex'

  import A17EditorSidebar from '@/components/editor/EditorSidebar.vue'
  import A17EditorPreview from '@/components/editor/EditorPreview.vue'
  import A17Spinner from '@/components/Spinner.vue'

  import { PREVIEW, CONTENT } from '@/store/mutations'
  import ACTIONS from '@/store/actions'

  import htmlClasses from '@/utils/htmlClasses'

  import cloneDeep from 'lodash/cloneDeep'

  const html = document.documentElement
  const htmlClass = htmlClasses.editor

  export default {
    name: 'A17Editor',
    components: {
      'a17-editorsidebar': A17EditorSidebar,
      'a17-editorpreview': A17EditorPreview,
      'a17-spinner': A17Spinner
    },
    props: {
      bgColor: {
        type: String,
        default: '#FFFFFF'
      }
    },
    data: function () {
      return {
        isWatching: false,
        unSubscribe: function () {
          return null
        }
      }
    },
    computed: {
      blocks: {
        get () {
          return this.savedBlocks
        },
        set (value) {
          this.$store.commit(CONTENT.REORDER_BLOCKS, value)
        }
      },
      hasBlockActive: function () {
        return Object.keys(this.activeBlock).length > 0
      },
      previewClass: function () {
        const bgColorObj = tinyColor(this.bgColor)
        return {
          'editor__preview--dark': bgColorObj.getBrightness() < 180,
          'editor__preview--loading': this.loading
        }
      },
      sidebarClass: function () {
        return {
          'editor__sidebar--mobile': this.hasBlockActive
        }
      },
      previewStyle: function () {
        return { 'background-color': this.bgColor }
      },
      ...mapState({
        loading: state => state.content.loading,
        activeBlock: state => state.content.active,
        savedBlocks: state => state.content.blocks,
        availableBlocks: state => state.content.available,
        revisions: state => state.revision.all
      })
    },
    watch: {
      loading: function (loading) {
        let self = this

        if (!loading) {
          self.$nextTick(function () {
            setTimeout(function () {
              self.scrollToActive()
            }, 250)
          })
        }
      }
    },
    methods: {
      open: function (index) {
        this.getAllPreviews()
        if (index >= 0) {
          this.selectBlock(index)
          this.scrollToActive()
        }

        this.$refs.overlay.open()
      },
      close: function (index) {
        this.$refs.overlay.close()
      },
      openEditor: function () {
        html.classList.add(htmlClass)
      },
      closeEditor: function () {
        this.unselectBlock()
        html.classList.remove(htmlClass)
      },
      scrollToActive: function () {
        if (!this.hasBlockActive) return

        const activeBlockEl = this.$refs.previews.$refs[this.activeBlock.id]
        if (activeBlockEl) {
          const activeScrollTop = activeBlockEl[0].offsetTop
          const scrollContainer = this.$el.querySelector('.editorPreview__content')
          scrollContainer.scrollTop = Math.max(0, activeScrollTop - 20)
        }
      },
      isBlockActive: function (id) {
        if (!this.hasBlockActive) return false

        return id === this.activeBlock.id
      },
      openPreview: function () {
        if (this.$root.$refs.preview) this.$root.$refs.preview.open()
      },
      resize: function () {
        let self = this
        window.addEventListener('mousemove', self.resizeSidebar, false)
        window.addEventListener('mouseup', self.stopResizeSidebar, false)
      },
      resizeSidebar: function (event) {
        const sidebar = this.$refs.sidebar
        const windowWidth = window.innerWidth
        if (sidebar) sidebar.style.width = (event.clientX - sidebar.offsetLeft) / windowWidth * 100 + '%'
      },
      stopResizeSidebar: function () {
        let self = this
        window.removeEventListener('mousemove', self.resizeSidebar, false)
        window.removeEventListener('mouseup', self.stopResizeSidebar, false)

        // resize all previews
        this.$refs.previews.resizeAllIframes()
      },
      saveBlock: function () {
        // refresh Preview
        if (this.hasBlockActive) this.getPreview()
        this.unselectBlock()
      },
      addBlock: function (index) {
        this.selectBlock(index)
        this.getPreview(index)
      },
      deleteBlock: function (index) {
        // open confirm dialog if any
        if (this.$root.$refs.warningContentEditor) {
          this.$root.$refs.warningContentEditor.open(() => {
            this.unselectBlock()
            this.$store.commit(CONTENT.DELETE_BLOCK, index)
          })
        } else {
          this.unselectBlock()
          this.$store.commit(CONTENT.DELETE_BLOCK, index)
        }
      },
      cancelBlock: function () {
        if (this.hasBlockActive) {
          if (window.hasOwnProperty('PREVSTATE')) {
            console.warn('Store - Restore previous Store state')
            this.$store.replaceState(window.PREVSTATE)
          }
          this.getPreview()
        }
        this.unselectBlock()
      },
      getBlockId: function (index) {
        if (typeof this.blocks[index] === 'undefined') return 0
        else return this.blocks[index].id
      },
      getAllPreviews: function () {
        this.$store.dispatch(ACTIONS.GET_ALL_PREVIEWS)
      },
      getPreview: function (index = -1) {
        console.warn('Editor - getPreview')
        this.$store.dispatch(ACTIONS.GET_PREVIEW, index)
      },
      selectBlock: function (index) {
        let self = this

        // toggle selection
        const blockId = this.getBlockId(index)
        if (!blockId) return

        if (this.isBlockActive(blockId)) this.unselectBlock()
        else {
          // Save current Store and activate
          console.warn('Store - copy current Store state')
          window.PREVSTATE = cloneDeep(this.$store.state)
          this.$store.commit(CONTENT.ACTIVATE_BLOCK, index)

          if (!this.isWatching) {
            this.isWatching = true
            this.unSubscribe = this.$store.subscribe((mutation, state) => {
              // Don't trigger a refresh of the preview every single time, just when necessary
              if (PREVIEW.REFRESH_BLOCK_PREVIEW.includes(mutation.type)) {
                console.log('Editor - store changed : ' + mutation.type)
                if (PREVIEW.REFRESH_BLOCK_PREVIEW_ALL.includes(mutation.type)) {
                  self.getAllPreviews()
                } else {
                  self.getPreview()
                }
              }
            })
          }
        }
      },
      unselectBlock: function () {
        this.unSubscribe()
        this.isWatching = false

        // remove prevstate
        if (window.hasOwnProperty('PREVSTATE')) delete window.PREVSTATE

        if (!this.hasBlockActive) return
        this.$store.commit(CONTENT.ACTIVATE_BLOCK, -1)
      }
    },
    mounted: function () {
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  $height__nav: 80px;

  .editor {
    display: block;
    width: 100%;
    padding: 0;
    position:relative;
    flex-grow:1;
    background-color:$color__background;
  }

  .editor__leave {
    position:fixed;
    right:20px;
    top:13px;
    z-index:$zindex__overlay + 1;
  }

  .editor__frame {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display:flex;
    flex-flow: column nowrap;
  }

  .editor__inner {
    position: relative;
    width: 100%;
    overflow: hidden;
    flex-grow: 1;
    display:flex;
    flex-flow: row nowrap;
    // height: calc(100vh - 60px);
  }

  .editor__sidebar {
    background:$color__border--light;
    width:30vw;
    min-width:400px;

    @include breakpoint('small-') {
      display: none;
    }
  }

  .editor__sidebar--mobile {
    @include breakpoint('small-') {
      display: block;
    }
  }

  .editor__resizer {
    width:10px;
    min-width: 10px;
    cursor: col-resize;
    background:$color__border--light;
    display: flex;
    align-items: center;
    justify-content: space-between;
    user-select:none;

    span {
      width:2px;
      height:20px;
      display:block;
      background: dragGrid__dots($color__drag);
      overflow:hidden;
      margin-left:auto;
      margin-right:auto;
    }
  }

  .editor__preview {
    flex-grow:1;
    position:relative;
    min-width:300px;
    color:$color__text--light;
  }

  .editor__preview--dark {
    color:$color__background;
  }

</style>
