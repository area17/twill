<template>
  <div class="fieldset" :class="visibilityClasses">
    <header v-if="title" class="fieldset__header" :class="activeClasses">
      <h3 v-if="activeToggle" @click="onClickVisibility" role="button" :aria-expanded="visible ?  'true' : 'false'" >{{ title}} <span v-svg symbol="dropdown_module"></span></h3>
      <h3 v-else>{{ title }}</h3>
    </header>
    <header v-else="" class="fieldset__header" :class="activeClasses">
      <slot name="header"></slot>
    </header>

    <div class="fieldset__content" :hidden="!visible ?  true : null" :aria-hidden="!visible ?  true : null">
      <slot></slot>
    </div>
  </div>
</template>

<script>
  import VisibilityMixin from '@/mixins/toggleVisibility'

  export default {
    name: 'A17Fieldset',
    mixins: [VisibilityMixin],
    props: {
      open: {
        type: Boolean,
        default: true
      },
      title: {
        default: ''
      },
      activeToggle: {
        type: Boolean,
        default: true
      }
    },
    computed: {
      activeClasses: function () {
        return { 'fieldset--hoverable': this.activeToggle }
      }
    }
  }
</script>

<style lang="scss" scoped>

  .fieldset {
    border-radius: 2px;
    border: 1px solid $color__border;
    margin-bottom: 20px;
    background:$color__background;
  }

  .fieldset__header {
    position: relative;
    height: 50px;
    margin: -1px;
    white-space: nowrap;
    background: $color__border;
    border-radius: 2px;

    h2, h3, h4 {
      height: 50px;
      line-height: 50px;
      padding: 1px 21px 0 21px;
      margin: 0;
      font-weight: 600;
      border-radius: 2px;
      user-select:none;
    }

    .icon {
      float: right;
      display: block;
      position: absolute;
      right: 20px;
      top: 50%;
      margin-top: -3px;
      color: $color__icons;
      transition: transform .25s linear;
    }
  }

  .fieldset--hoverable {
    h2, h3, h4 {
      cursor: pointer;

      &:hover,
      &:focus {
        background: $color__border--hover
      }
    }
  }

  .fieldset__content {
    > h2,
    > h3,
    > h4 {
      font-size: 1em;
      font-weight: 600;
      margin-top: 35px;
    }

    > p {
      margin-top: 35px;
    }

    > hr {
      height: 5px;
      margin: 50px -20px 20px -20px;
      padding: 0;
      background: $color__border--light;
      border: 0 none;

      + .repeater {
        margin-top:20px;
      }

      + .blocks + hr {
        margin-top:20px;
      }
    }
  }

  .fieldset__content {
    padding: 0 20px 20px 20px;
    display: none;
  }

  .s--open {
    .fieldset__header {
      margin-bottom: 0;
      border-radius: 2px 2px 0 0;

      h2, h3, h4 {
        border-radius: 2px 2px 0 0;
      }
    }

    .fieldset__header .icon {
      transform: rotate(180deg);
    }

    .fieldset__content {
      display: block;
    }
  }
</style>
