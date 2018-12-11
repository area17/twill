<template>
  <draggable class="editorSidebar__blocks"
             :class="editorSidebarClasses"
             v-model="blocks"
             :options="{
                   group: {
                    name: 'editorBlocks',
                    pull: 'clone',
                    put: false
                   },
                   handle: '.editorSidebar__button'
                  }">
    <div class="editorSidebar__button"
         :data-title="block.title"
         :data-icon="block.icon"
         :data-component="block.component"
         v-for="block in blocks"
         :key="block.component">
            <span v-svg
                  :symbol="block.icon"></span>
      {{ block.title }}
    </div>
  </draggable>
</template>

<script>
  import draggable from 'vuedraggable'
  import { DraggableMixin } from '@/mixins'

  export default {
    name: 'A17EditorSidebarBlockList',
    props: {
      blocks: {
        type: Array,
        default: () => []
      },
      inFieldset: {
        type: Boolean,
        default: false
      }
    },
    mixins: [DraggableMixin],
    components: {
      draggable
    },
    computed: {
      editorSidebarClasses () {
        return {
          'editorSidebar__blocks--in-fieldset': this.inFieldset
        }
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .editorSidebar__blocks--in-fieldset {
    padding-top: 20px;

    .editorSidebar__button:last-child {
      padding-bottom: 0;
    }
  }

  .editorSidebar__button {
    @include btn-reset;
    cursor: move;
    display: block;
    width: 100%;
    text-align: left;
    background: $color__background;
    border-radius: $border-radius;
    margin-bottom: 10px;
    height: 60px;
    line-height: 60px;
    padding: 0 20px;
    border: 1px solid $color__border;
    color: $color__text--light;

    .icon {
      margin-left: -20px;
      min-width: 65px;
      text-align: center;
      color: $color__icons;
      height: 60px - 2px;
    }

    &:hover,
    &:focus {
      color: $color__text;
      border-color: $color__border--focus;

      .icon {
        color: $color__text;
      }
    }
  }
</style>
