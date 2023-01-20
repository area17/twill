<template>
  <div class="editorSidebar__listItems">
    <!-- eslint-disable vue/no-mutating-props -->
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
      <!--eslint-enable-->
      <div
          class="editorSidebar__button"
          :data-title="block.title"
          :data-icon="block.icon"
          :data-component="block.component"
          v-for="block in blocks"
          :key="block.component"
      >
        <span v-svg :symbol="iconSymbol(block.icon)"></span>
        <span class="editorSidebar__buttonLabel">{{ block.title }}</span>
      </div>
    </draggable>
  </div>
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
    },
    methods: {
      iconSymbol: function (icon) {
        // Future block editor icons will have two variations: small and large.
        // Small formats will be used by default in the dropdown, and large
        // formats (named with `-lg` suffix) will be used in the sidebar.
        return this.hasLgIconVariation(icon) ? `${icon}-lg` : icon
      },
      hasLgIconVariation: function (icon) {
        return Boolean(document.querySelector(`#icon--${icon}-lg`))
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

  .editorSidebar__listItems > div {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
  }

  .editorSidebar__button {
    @include btn-reset;
    @include font-tiny-btn;
    cursor: move;
    display: flex;
    flex-direction: column;
    width: calc(50% - 5px);
    height: 100px;
    padding: 8px 20px;
    margin-bottom: 10px;
    background: $color__background;
    border-radius: $border-radius;
    border: 1px solid $color__border;
    color: $color__text--light;
    text-align: center;

    .icon {
      flex-grow: 1;
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: $color__icons;
    }

    .editorSidebar__buttonLabel {
      width: 100%;
      line-height: 1;
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

  .editorPreview__content {
    .editorSidebar__button {
      // use full width instead of half for buttons being dragged to the content area
      width: 100%;
    }
  }
</style>
