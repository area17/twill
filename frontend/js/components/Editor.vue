<template>
  <a17-overlay ref="overlay"
               :title="$trans('editor.title')"
               :customClasses="htmlEditorClass"
               @close="close">
    <a17-block-list :section="section">

      <div class="editor"
           slot-scope="{ availableBlocks, hasBlockActive, savedBlocks, sections, multipleSections, reorderBlocks, moveBlock }">

        <a17-button v-if="revisions.length"
                    class="editor__leave"
                    variant="editor"
                    size="small"
                    @click="openPreview">

        <span class="hide--xsmall"
              v-svg
              symbol="preview"></span>{{ $trans('fields.block-editor.preview', 'Preview') }}
        </a17-button>

        <div class="editor__frame">
          <div class="editor__inner">
            <div class="editor__sidebar" ref="sidebar">
              <a17-editorsidebar :section="section"
                                 :specificSection="specificSection"
                                 :multipleSections="multipleSections"
                                 :hasBlockActive="hasBlockActive"
                                 :sections="sections"
                                 :blocks="availableBlocks"
                                 :savedBlocksLength="savedBlocks.length"
                                 @section:update="updateSection">
                {{ $trans('fields.block-editor.add-content', 'Add content') }}
              </a17-editorsidebar>
            </div>
            <div class="editor__resizer"
                 @mousedown="resize"><span></span></div>
            <div class="editor__preview">
              <a17-editorpreview ref="previews"
                                 v-if="editorOpen"
                                 :section="section"
                                 :blocks="savedBlocks"
                                 :hasBlockActive="hasBlockActive"
                                 :sandbox="previewSandbox"
                                 :bgColor="bgColor"
                                 @blocks:move="moveBlock"/>
            </div>
          </div>
        </div>
      </div>
    </a17-block-list>
  </a17-overlay>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'

  import A17EditorSidebar from '@/components/editor/EditorSidebar.vue'
  import A17EditorPreview from '@/components/editor/EditorPreview.vue'
  import A17BlocksList from '@/components/blocks/BlocksList'

  import htmlClasses from '@/utils/htmlClasses'

  export default {
    name: 'A17Editor',
    components: {
      'a17-editorsidebar': A17EditorSidebar,
      'a17-editorpreview': A17EditorPreview,
      'a17-block-list': A17BlocksList
    },
    props: {
      bgColor: {
        type: String,
        default: '#FFFFFF'
      },
      previewSandbox: {
        type: [Boolean, Array],
        default: true
      }
    },
    data () {
      return {
        specificSection: false,
        section: 'default',
        editorOpen: false,
        htmlEditorClass: htmlClasses.editor
      }
    },
    computed: {
      ...mapState({
        revisions: state => state.revision.all
      }),
      ...mapGetters([
        'savedBlocksBySection'
      ])
    },
    provide () {
      return {
        sandbox: this.previewSandbox
      }
    },
    methods: {
      // Section functions
      initSection () {
        this.updateSection('default', false)
      },
      updateSection (section, specificSection = false) {
        this.section = section
        this.specificSection = specificSection
      },
      // Editor state functions
      open (index, section = false) {
        if (section) {
          this.updateSection(section, true)
        }

        this.editorOpen = true

        this.$refs.overlay.open()
      },
      close () {
        this.initSection()
        this.editorOpen = false
      },
      resize () {
        window.addEventListener('mousemove', this.resizeSidebar, false)
        window.addEventListener('mouseup', this.stopResizeSidebar, false)
      },
      resizeSidebar (event) {
        const sidebar = this.$refs.sidebar
        const windowWidth = window.innerWidth
        if (sidebar) sidebar.style.width = (event.clientX - sidebar.offsetLeft) / windowWidth * 100 + '%'
      },
      stopResizeSidebar () {
        window.removeEventListener('mousemove', this.resizeSidebar, false)
        window.removeEventListener('mouseup', this.stopResizeSidebar, false)

        // resize all previews
        this.$refs.previews.resizeAllIframes()
      },

      // Open Revision modal
      openPreview () {
        if (this.$root.$refs.preview) this.$root.$refs.preview.open()
      }
    }
  }
</script>

<style lang="scss" scoped>

  $height__nav: 80px;

  .editor {
    display: block;
    width: 100%;
    padding: 0;
    position: relative;
    flex-grow: 1;
    background-color: $color__background;
  }

  .editor__leave {
    position: fixed;
    right: 20px;
    top: 13px;
    z-index: $zindex__overlay + 1;
  }

  .editor__frame {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;
    flex-flow: column nowrap;
  }

  .editor__inner {
    position: relative;
    width: 100%;
    overflow: hidden;
    flex-grow: 1;
    display: flex;
    flex-flow: row nowrap;
    // height: calc(100vh - 60px);
  }

  .editor__sidebar {
    background: $color__border--light;
    width: 30vw;
    min-width: 400px;
  }

  .editor__resizer {
    width: 10px;
    min-width: 10px;
    cursor: col-resize;
    background: $color__border--light;
    display: flex;
    align-items: center;
    justify-content: space-between;
    user-select: none;

    span {
      width: 2px;
      height: 20px;
      display: block;
      background: dragGrid__dots($color__drag);
      overflow: hidden;
      margin-left: auto;
      margin-right: auto;
    }
  }

  .editor__preview {
    flex-grow: 1;
    position: relative;
    min-width: 300px;
    color: $color__text--light;
  }

  .editor__preview--dark {
    color: $color__background;
  }
</style>
