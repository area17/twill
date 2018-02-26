<template>
  <a17-overlay ref="overlay" title="Content editor" @close="closeEditor" @open="openEditor">
    <div class="editor">
      <a17-button class="editor__leave" variant="editor" size="small" @click="openPreview" v-if="revisions.length"><span v-svg symbol="preview"></span>Preview</a17-button>
      <div class="editor__frame">
        <div class="editor__inner">
          <div class="editor__sidebar" ref="sidebar">
            <a17-editorsidebar @delete="deleteBlock" @save="saveBlock" @cancel="cancelBlock">Add content</a17-editorsidebar>
          </div>
          <div class="editor__resizer" @mousedown="resize"><span></span></div>
          <div class="editor__preview" :style="previewStyle">
            <a17-editorpreview ref="previews" @select="selectBlock" @delete="deleteBlock" @unselect="unselectBlock" @add="addBlock" />
            <a17-spinner v-if="loading">Loading&hellip;</a17-spinner>
          </div>
        </div>
      </div>
    </div>
  </a17-overlay>
</template>

<script>
  import { mapState } from 'vuex'

  import A17EditorSidebar from '@/components/editor/EditorSidebar.vue'
  import A17EditorPreview from '@/components/editor/EditorPreview.vue'
  import A17Spinner from '@/components/Spinner.vue'

  import * as mutationTypes from '@/store/mutation-types'

  import cloneDeep from 'lodash/cloneDeep'

  const html = document.documentElement
  let htmlClass = 's--in-editor'

  export default {
    name: 'A17editor',
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
          this.$store.commit('reorderBlocks', value)
        }
      },
      hasBlockActive: function () {
        return Object.keys(this.activeBlock).length > 0
      },
      previewStyle: function () {
        return {
          'background-color': this.bgColor
        }
      },
      ...mapState({
        loading: state => state.content.loading,
        activeBlock: state => state.content.active,
        savedBlocks: state => state.content.blocks,
        availableBlocks: state => state.content.available,
        revisions: state => state.revision.all
      })
    },
    methods: {
      open: function (index) {
        if (index >= 0) this.selectBlock(index)
        this.$refs.overlay.open()
      },
      close: function (index) {
        this.$refs.overlay.close()
      },
      openEditor: function () {
        this.getAllPreviews()
        html.classList.add(htmlClass)
      },
      closeEditor: function () {
        console.log('closeEditor')
        this.unselectBlock()
        html.classList.remove(htmlClass)
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
        this.unselectBlock()
        this.$store.commit('deleteBlock', index)
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
        this.$store.dispatch('getAllPreviews')
      },
      getPreview: function (index = -1) {
        console.warn('Editor - getPreview')
        this.$store.dispatch('getPreview', index)
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
          this.$store.commit('activateBlock', index)

          if (!this.isWatching) {
            this.isWatching = true
            this.unSubscribe = this.$store.subscribe((mutation, state) => {
              // Don't trigger a refresh of the preview every single time, just when necessary
              if (mutationTypes.REFRESH_BLOCK_PREVIEW.includes(mutation.type)) {
                console.log('Editor - store changed : ' + mutation.type)
                if (mutationTypes.REFRESH_BLOCK_PREVIEW_ALL.includes(mutation.type)) {
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
        this.$store.commit('activateBlock', -1)
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
  }

  .editor__sidebar {
    background:$color__border--light;
    width:30vw;
    min-width:400px;
  }

  .editor__resizer {
    width:10px;
    min-width: 10px;
    cursor: e-resize;
    background:$color__border--light;
    display: flex;
    align-items: center;
    justify-content: space-between;

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
  }
</style>
