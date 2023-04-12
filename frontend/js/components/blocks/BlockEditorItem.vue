<template>
  <div class="block" :class="blockClasses">
    <div class="block__header" @dblclick.prevent="toggleExpand()">
      <span v-if="withHandle" class="block__handle"></span>
      <div class="block__toggle">
        <a17-dropdown :ref="moveDropdown" class="f--small" position="bottom-left" v-if="withMoveDropdown" :maxHeight="270">
          <span class="block__counter f--tiny" @click="$refs[moveDropdown].toggle()">{{ index + 1 }}</span>
          <div slot="dropdown__content">
            <slot name="dropdown-numbers"/>
          </div>
        </a17-dropdown>
        <span class="block__counter f--tiny" v-else>{{ index + 1 }}</span>
        <span class="block__title">{{ blockTitle }}</span>
      </div>
      <div class="block__actions" v-if="withActions">
        <slot name="block-actions"/>
        <a17-dropdown :ref="addDropdown" position="bottom-right" :maxHeight="430" @open="hover = true" @close="hover = false" v-if="withAddDropdown">
          <a17-button variant="icon" data-action @click="$refs[addDropdown].toggle()"><span v-svg symbol="add"></span>
          </a17-button>
          <div slot="dropdown__content">
            <slot name="dropdown-add"/>
          </div>
        </a17-dropdown>

        <a17-button variant="icon" data-action @click="toggleExpand()" :aria-expanded="visible ? 'true' : 'false'"><span
          v-svg symbol="expand"></span></a17-button>

        <a17-dropdown :ref="actionsDropdown" position="bottom-right" @open="hover = true" @close="hover = false">
          <a17-button variant="icon" @click="$refs[actionsDropdown].toggle()"><span v-svg symbol="more-dots"></span>
          </a17-button>
          <div slot="dropdown__content">
            <slot name="dropdown-action"/>
          </div>
        </a17-dropdown>
      </div>
    </div>
    <div class="block__content" v-if="visible">
      <component v-bind:is="`${block.type}`" :name="componentName(block.id)" v-bind="block.attributes" :key="`form_${block.type}_${block.id}`">
        <!-- dynamic components -->
      </component>
      <!-- Block validation input frame, to display errors -->
      <a17-inputframe size="small" label="" :name="`block.${block.id}`"></a17-inputframe>
    </div>
  </div>
</template>

<script>
  import { mapGetters,mapState } from 'vuex'

  import a17VueFilters from '@/utils/filters.js'

  export default {
    name: 'A17BlockEditorItem',
    props: {
      index: {
        type: Number,
        default: 0
      },
      opened: {
        type: Boolean,
        default: true
      },
      size: {
        type: String,
        default: '' // small
      },
      block: {
        type: Object,
        default: () => {}
      },
      withHandle: {
        type: Boolean,
        default: true
      },
      withActions: {
        type: Boolean,
        default: true
      }
    },
    data () {
      return {
        visible: false,
        hover: false,
        withMoveDropdown: true,
        withAddDropdown: true
      }
    },
    filters: a17VueFilters,
    computed: {
      titleFieldValue: function () {
        const fieldValue = this.blockFieldValue(this.block.titleField)
        if (!fieldValue) return null

        if (typeof fieldValue === 'object') {
          return fieldValue[this.currentLocale.value]
        }

        return fieldValue
      },
      blockTitle: function () {
        const title = this.block.title || ''
        const suffix = this.titleFieldValue || ''
        const separator = title && suffix ? ' — ' : ''

        let fullTitle

        if (this.block.hideTitlePrefix) {
          fullTitle = `${suffix}`
        } else {
          fullTitle = `${title}${separator}${suffix}`
        }

        const cleanup = document.createElement('div')
        cleanup.innerHTML = fullTitle
        return cleanup.innerText
      },
      blockClasses () {
        return [
          this.visible ? 'block--open' : '',
          this.hover ? 'block--focus' : '',
          this.size ? `block--${this.size}` : ''
        ]
      },
      moveDropdown () {
        return `moveBlock${this.index}Dropdown`
      },
      actionsDropdown () {
        return `action${this.block.id}Dropdown`
      },
      addDropdown () {
        return `add${this.block.id}Dropdown`
      },
      ...mapState({
        currentLocale: state => state.language.active
      }),
      ...mapGetters([
        'fieldValueByName'
      ])
    },
    watch: {
      opened () {
        this.visible = this.opened
      }
    },
    created () {
      if (this.block.ui && this.block.ui.isNew) {
        this.toggleExpand()
      }
    },
    methods: {
      toggleExpand () {
        this.visible = !this.visible
      },
      componentName (id) {
        return 'blocks[' + id + ']'
      },
      blockFieldName: function (fieldName) {
        if (!fieldName) return ''

        return `blocks[${this.block.id}][${fieldName}]`
      },
      blockFieldValue: function (fieldName) {
        if (!fieldName) return null

        const blockFieldName = this.blockFieldName(fieldName)
        return this.fieldValueByName(blockFieldName)
      }
    },
    beforeMount () {
      if (!this.$slots['dropdown-numbers']) this.withMoveDropdown = false
      if (!this.$slots['dropdown-add']) this.withAddDropdown = false
    }
  }
</script>

<style lang="scss" scoped>
  .block__content {
    display: none;
    padding: 15px;
    background: $color__background;
  }

  .block--open {
    > .block__content {
      display: block;
    }

    > .block__header {
      border-bottom: 1px solid $color__border--light;
    }
  }

  .block__header {
    height: 50px;
    line-height: 50px;
    background: $color__block-bg;
    padding: 0 15px;
    position: relative;
    display: flex;
    background-clip: padding-box;
  }

  .block__handle {
    position: absolute;
    height: 10px;
    width: 40px;
    left: 50%;
    top: 50%;
    margin-left: -20px;
    margin-top: -5px;
    @include dragGrid($color__drag, $color__block-bg);
  }

  .block__counter {
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
    margin-top: calc((50px - 26px) / 2);
  }

  .dropdown .block__counter {
    cursor: pointer;

    &:hover {
      color: $color__text;
      border-color: $color__text;
    }
  }

  .dropdown--active .block__counter {
    color: $color__text;
    border-color: $color__text;
  }

  .block__title {
    text-overflow: ellipsis;
    font-weight: 600;
    overflow: hidden;
    display: inline-block;
    white-space: nowrap;
    height: 50px;
    line-height: 50px;
    user-select: none;
  }

  .block__toggle {
    flex-grow: 1;
    display: flex;
    max-width: 50%;
    padding-right: 30px;

    .dropdown {
      display: inline-block;
      vertical-align: top;
    }

    .block__counter {
      vertical-align: top;
    }
  }

  .block__actions {
    text-align: right;
    font-size: 0px;
    padding-top: calc((50px - 26px) / 2);
    padding-bottom: calc((50px - 26px) / 2);
    margin-left: auto;

    > * {
      margin-left: 10px;
      @include font-regular();
    }

    > button,
    .dropdown,
    .dropdown > button {
      display: inline-block;
      vertical-align: top;
      height: 26px;
    }
  }

  .block__actions {
    button[data-action] {
      visibility: hidden;
    }

    .dropdown--active button[data-action] {
      visibility: visible;
      display: inline-block;
    }
  }

  .block__header:hover {
    background: $color__block-bg--hover;

    .block__handle {
      &:before {
        background: dragGrid__bg($color__block-bg--hover);
      }
    }

    button[data-action] {
      visibility: visible;
      display: inline-block;
    }
  }

  .block__header:hover,
  .block--focus .block__header {
    button[data-action] {
      display: inline-block;
    }
  }

  /* Media field in block */
  .block__content {
    > .media,
    > .slideshow,
    > .browserField {
      margin: -15px;
      border: 0 none;
    }

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

  // Small blocks (for repeater inside the block editor)
  .block--small {
    .block__header {
      background: $color__f--bg;

      .block__handle {
        background: dragGrid__dots($color__drag);

        &:before {
          background: dragGrid__bg($color__f--bg);
        }
      }
    }

    .block__header:hover {
      background: $color__light;

      .block__handle:before {
        background: dragGrid__bg($color__light);
      }
    }

    .block__counter {
      display: none;
    }
  }

</style>

<style lang="scss">
  .block {
    .block__content {
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
  }
</style>
