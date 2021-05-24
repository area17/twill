<template>
  <div class="editorSidebar__item">
    <div class="editorSidebar__title">
      <div class="editorSidebar__blockTitle">
        <a17-dropdown
          class="f--small"
          position="bottom-left"
          ref="blockDropdown"
          :maxHeight="270">
            <span class="editorSidebar__counter f--tiny"
                  @click="toggleBlockDropdown">{{ blockIndex + 1 }}</span>
          <div slot="dropdown__content">
            <button type="button"
                    v-for="n in blocksLength"
                    @click="moveBlock(n - 1)"
                    :key="n">
              {{ n }}
            </button>
          </div>
        </a17-dropdown>
        {{ block.title }}
      </div>
      <span>
          <a href="#"
             class="f--small f--note f--underlined"
             @click.prevent="deleteBlock">{{ $trans('editor.delete') }}</a>
        </span>
    </div>
    <div class="editorSidebar__body">
      <a17-inputframe label=""
                      :name="`block.${block.id}`"/>
      <template>
        <component :name="`blocks[${block.id}]`"
                   v-bind:is="`${block.type}`"
                   v-bind="setBlockAttributes(block.attributes)"
                   key="`editor_${block.type}_${block.id}`"/>
      </template>

    </div>
  </div>
</template>

<script>
  import { BlockItemMixin } from '@/mixins'

  export default {
    name: 'A17EditorSidebarBlockItem',
    mixins: [BlockItemMixin],
    methods: {
      setBlockAttributes (attributes) {
        return {
          keepAlive: true,
          ...attributes
        }
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/mixins-colors-vars';

  .editorSidebar__item {
    padding: 0 10px 0 20px;
    overflow-y: auto;
    position: absolute;
    top: 0;
    bottom: 60px;
    left: 0;
    right: 0;
  }

  .editorSidebar__title {
    padding: 15px 0 10px 0;
    display: flex;
  }

  .editorSidebar__body {
    border: 1px solid $color__border;
    border-radius: 2px;
    background: $color__background;
    padding: 15px;

    ::v-deep(.input) {
      margin-top: 15px;
    }

    ::v-deep(.block__body) {
      > .media,
      > .slideshow,
      > .browserField {
        margin-left: -15px;
        margin-right: -15px;
        border: 0 none;
      }

      > .media:last-child,
      > .slideshow:last-child,
      > .browserField:last-child {
        margin-bottom: -15px;
      }
    }
  }

  .editorSidebar__counter {
    border: 1px solid $color__border;
    border-radius: 50%;
    height: 26px;
    width: 26px;
    text-align: center;
    display: inline-block;
    line-height: 25px;
    margin-right: 10px;
    background: $color__background;
    color: $color__text--light;
    @include monospaced-figures('off'); // dont use monospaced figures here
    user-select: none;
    cursor: default;
  }

  .dropdown .editorSidebar__counter {
    cursor: pointer;
  }

  .editorSidebar__counter:hover,
  .dropdown--active .editorSidebar__counter {
    color: $color__text;
    border-color: $color__text;
  }

  .editorSidebar__blockTitle {
    font-weight: 600;
  }

  .editorSidebar__blockTitle {
    flex-grow: 1;

    .dropdown {
      display: inline-block;
    }
  }
</style>
