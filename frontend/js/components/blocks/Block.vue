<template>
  <div class="block" :class="{ 'block--open': visible, 'block--focus' : hover }">
    <div class="block__header">
      <span class="block__counter f--tiny">{{ index + 1 }}</span>
      <span class="block__title">{{ block.title }}</span>
      <span class="block__handle"></span>

      <div class="block__actions">
        <slot name="block-actions"></slot>
        <a17-dropdown :ref="addDropdown" position="bottom-right" @open="hover = true" @close="hover = false" :offset="-3" v-if="withAddDropdown">
          <a17-button variant="icon" data-action @click="$refs[addDropdown].toggle()"><span v-svg symbol="add"></span></a17-button>
          <div slot="dropdown__content">
            <slot name="dropdown-add"></slot>
          </div>
        </a17-dropdown>

        <a17-button variant="icon" data-action @click="visible = !visible" :aria-expanded="visible ?  'true' : 'false'"><span v-svg symbol="expand"></span></a17-button>

        <a17-dropdown :ref="actionsDropdown" position="bottom-right" @open="hover = true" @close="hover = false" :offset="-3">
          <a17-button variant="icon" @click="$refs[actionsDropdown].toggle()"><span v-svg symbol="more-dots"></span></a17-button>
          <div slot="dropdown__content">
            <slot name="dropdown-action"></slot>
          </div>
        </a17-dropdown>
      </div>
    </div>
    <div class="block__content" :aria-hidden="!visible ?  true : null">
      <component v-bind:is="`${block.type}`" :name="componentName(block.id)" v-bind="block.attributes"><!-- dynamic components --></component>
    </div>
  </div>
</template>

<script>
  import a17VueFilters from '@/utils/filters.js'

  export default {
    name: 'A17Block',
    props: {
      index: {
        type: Number,
        default: 0
      },
      block: {
        type: Object,
        default: function () {
          return {}
        }
      }
    },
    data: function () {
      return {
        visible: true,
        hover: false,
        withAddDropdown: true
      }
    },
    filters: a17VueFilters,
    computed: {
      actionsDropdown: function () {
        return `action${this.block.id}Dropdown`
      },
      addDropdown: function () {
        return `add${this.block.id}Dropdown`
      }
    },
    methods: {
      componentName: function (id) {
        const slug = this.$options.filters.slugify(this.block.title)
        return slug + '[' + id + ']' // [' + type + ']' // [" + name + "]";
      }
    },
    beforeMount: function () {
      if (!this.$slots['dropdown-add']) this.withAddDropdown = false
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .block__content {
    display:none;
    padding:15px;
    background:$color__background;
  }

  .block--open {
    .block__content {
      display:block;
    }

    .block__header {
      border-bottom:1px solid $color__border--light;
    }
  }

  .block__header {
    height:50px;
    line-height:50px;
    background:$color__f--bg;
    padding:0 15px;
    position:relative;
    display:flex;
  }

  .block__counter {
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
    margin-top:(50px - 26px) / 2
  }

  .block__title {
    font-weight:bold;
    height:50px;
    line-height:50px;
  }

  .block__actions {
    flex-grow:1;
    text-align:right;
    font-size:0px;

    > * {
      display:inline-block;
      margin-left: 10px;
      @include font-regular();
    }
  }

  .block__handle {
    cursor: move;
    position:absolute;
    background:repeating-linear-gradient(180deg, $color__drag 0, $color__drag 2px, transparent 2px, transparent 4px);
    height:10px;
    width:40px;
    left:50%;
    top:50%;
    margin-left:-20px;
    margin-top:-5px;

    &:before {
      display:block;
      content:'';
      background:repeating-linear-gradient(90deg, $color__light 0, $color__light 2px, transparent 2px, transparent 4px);
      width:100%;
      height:10px;
    }
  }

  .block__actions {
    button[data-action] {
      display:none;
    }

    .dropdown--active button[data-action] {
      display:inline-block;
    }
  }

  .block__header:hover {
    background:$color__light;

    button[data-action] {
      display:inline-block;
    }
  }

  .block__header:hover,
  .block--focus .block__header {
    button[data-action] {
      display:inline-block;
    }
  }

  /* Media field in block */
  .block__content {
    > .media,
    > .slideshow,
    > .browserField {
      margin:-15px;
      border:0 none;
    }
  }

  // .block__content {
  //   /deep/ .input {
  //     margin-top:15px;
  //   }
  // }
</style>
