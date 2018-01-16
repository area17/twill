<template>
  <div class="editor">
    <a17-button class="editor__leave" variant="editor" size="small" @click="openPreview"><span v-svg symbol="preview"></span>Preview</a17-button>
    <div class="editor__frame">
      <div class="editor__inner">
        <div class="editor__sidebar">
          <a17-editorsidebar>Add Content</a17-editorsidebar>
        </div>
        <div class="editor__preview">
          <a17-editorpreview>Preview</a17-editorpreview>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import { mapState } from 'vuex'

  import A17EditorSidebar from '@/components/editor/EditorSidebar.vue'
  import A17EditorPreview from '@/components/editor/EditorPreview.vue'

  export default {
    name: 'A17editor',
    components: {
      'a17-editorsidebar': A17EditorSidebar,
      'a17-editorpreview': A17EditorPreview
    },
    data: function () {
      return {
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
      ...mapState({
        savedBlocks: state => state.content.blocks,
        availableBlocks: state => state.content.available
      })
    },
    methods: {
      openPreview: function () {
        this.$store.commit('updateRevision', 0)
        if (this.$root.$refs.preview) this.$root.$refs.preview.open()
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
  }

  .editor__preview {
    flex-grow:1;
    position:relative;
  }
</style>
