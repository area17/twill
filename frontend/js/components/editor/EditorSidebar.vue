<template>
  <div class="editorSidebar">
    <div class="editorSidebar__item" v-for="(block, index) in blocks" :key="block.id" v-show="isBlockActive(block.id)">
      <div class="editorSidebar__title">
        <div class="editorSidebar__blockTitle">
          <a17-dropdown class="f--small" position="bottom-left" :ref="moveDropdown(index)" :maxHeight="270">
            <span class="editorSidebar__counter f--tiny" @click="toggleDropdown(index)">{{ index + 1 }}</span>
            <div slot="dropdown__content">
              <button type="button"
                      v-for="n in blocks.length"
                      :key="n"
                      @click="moveBlock(index, n - 1)">{{ n }}</button>
            </div>
          </a17-dropdown>{{ activeBlock.title }}
        </div>
        <span>
          <a href="#" @click.prevent="deleteBlock(index)" class="f--small f--note f--underlined">{{ $trans('editor.delete') }}</a>
        </span>
      </div>
      <div class="editorSidebar__body">
        <a17-inputframe label="" :name="`block.${block.id}`"/>
        <component v-bind:is="`${block.type}`" :name="componentName(block.id)" v-bind="block.attributes" key="`editor_${block.type}_${block.id}`"></component>
      </div>
    </div>
    <template v-if="!hasBlockActive">
      <div class="editorSidebar__list">
        <h4 class="editorSidebar__title"><slot></slot></h4>
        <div class="editorSidebar__listItems">
          <draggable v-model="availableBlocks" :options="{ group: { name: 'editorBlocks',  pull: 'clone', put: false }, handle: '.editorSidebar__button' }" v-if="availableBlocks.length">
            <div class="editorSidebar__button" :data-title="availableBlock.title" :data-icon="availableBlock.icon" :data-component="availableBlock.component" v-for="availableBlock in availableBlocks" :key="availableBlock.component">
              <span v-svg :symbol="iconSymbol(availableBlock.icon)"></span>
              <span class="editorSidebar__buttonLabel">{{ availableBlock.title }}</span>
            </div>
          </draggable>
        </div>
      </div>
      <div class="editorSidebar__actions">
        <a17-button v-if="isSubmitDisabled(submitOptions[0])" variant="validate" :disabled="true">{{ submitOptions[0].text }}</a17-button>
        <a17-button v-else @click="saveForm(submitOptions[0].name)" :name="submitOptions[0].name" variant="validate">{{ submitOptions[0].text }}</a17-button>
      </div>
    </template>
    <template v-else>
      <div class="editorSidebar__actions">
        <a17-button variant="action" @click="saveBlock()">{{ $trans('editor.done') }}</a17-button>
        <a17-button variant="secondary" @click="cancelBlock()">{{ $trans('editor.cancel') }}</a17-button>
      </div>
    </template>
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import { CONTENT, PUBLICATION } from '@/store/mutations'

  import draggableMixin from '@/mixins/draggable'
  import draggable from 'vuedraggable'

  export default {
    name: 'A17editorsidebar',
    components: {
      draggable
    },
    mixins: [draggableMixin],
    data: function () {
      return {
      }
    },
    computed: {
      hasBlockActive: function () {
        return Object.keys(this.activeBlock).length > 0
      },
      submitOptions: function () {
        return this.$store.getters.getSubmitOptions
      },
      ...mapState({
        activeBlock: state => state.content.active,
        availableBlocks: state => state.content.available,
        blocks: state => state.content.blocks
      })
    },
    methods: {
      isSubmitDisabled: function (btn) {
        if (btn.hasOwnProperty('disabled')) {
          return btn.disabled === true
        } else {
          return false
        }
      },
      toggleDropdown: function (index) {
        if (this.blocks.length > 1) {
          const ddName = this.moveDropdown(index)
          if (this.$refs[ddName].length) this.$refs[ddName][0].toggle()
        }
      },
      moveDropdown: function (index) {
        return `move${index}Dropdown`
      },
      isBlockActive: function (id) {
        if (!this.hasBlockActive) return false

        return id === this.activeBlock.id
      },
      componentName: function (id) {
        return 'blocks[' + id + ']'
      },
      moveBlock: function (oldIndex, newIndex) {
        if (oldIndex !== newIndex) {
          this.$store.commit(CONTENT.MOVE_BLOCK, {
            oldIndex: oldIndex,
            newIndex: newIndex
          })
        }
      },
      saveBlock: function () {
        this.$emit('save')
      },
      cancelBlock: function () {
        this.$emit('cancel')
      },
      deleteBlock: function (index) {
        this.$emit('delete', index)
      },
      saveForm: function (buttonName) {
        this.$store.commit(PUBLICATION.UPDATE_SAVE_TYPE, buttonName)
        if (this.$root.submitForm) this.$root.submitForm()
      },
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
  .editorSidebar {
    margin:20px 0 20px 0;
    // height:100%;
    position:relative;
    overflow: hidden;
    height: calc(100% - 40px);
  }

  .editorSidebar__item,
  .editorSidebar__list {
    padding:0 10px 0 20px;
    overflow-y: scroll;
    // height: calc(100% + 20px - 80px);
    position:absolute;
    top:0;
    bottom:60px;
    left:0;
    right:0;
  }

  .editorSidebar__list {
    height: calc(100% - 60px);
  }

  .editorSidebar__listItems > div {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
  }

  .editorSidebar__title {
    padding:15px 0 10px 0;
    display:flex;
  }

  .editorSidebar__body {
    border: 1px solid $color__border;
    border-radius:2px;
    background:$color__background;
    padding:15px;
  }

  .editorSidebar__counter {
    border:1px solid $color__border;
    border-radius:50%;
    height:26px;
    width:26px;
    text-align:center;
    display:inline-block;
    line-height:25px;
    margin-right:10px;
    background:$color__background;
    color:$color__text--light;
    @include monospaced-figures('off'); // dont use monospaced figures here
    user-select: none;
    cursor: default;
  }

  .dropdown .editorSidebar__counter {
    cursor: pointer;
  }

  .editorSidebar__counter:hover,
  .dropdown--active .editorSidebar__counter {
    color:$color__text;
    border-color:$color__text;
  }

  h4,
  .editorSidebar__blockTitle {
    font-weight:600;
  }

  .editorSidebar__blockTitle {
    flex-grow:1;

    .dropdown {
      display:inline-block;
    }
  }

  .editorSidebar__actions {
    position:absolute;
    width:100%;
    left:0;
    bottom:0;
    padding: 20px 10px 0 20px;
    background:$color__border--light;
    display:flex;

    button {
      width:calc(50% - 10px);
    }

    button + button {
      margin-left:20px;
    }

    button.button--validate:last-child {
      width:100%;
      margin-left:0;
    }
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
      color:$color__text;
      border-color:$color__border--focus;

      .icon {
        color:$color__text;
      }
    }
  }
</style>

<style lang="scss">
  .editorSidebar__body {
    .block__body {
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

  .editorPreview__content {
    .editorSidebar__button {
      // use full width instead of half for buttons being dragged to the content area
      width: 100%;
    }
  }
</style>
