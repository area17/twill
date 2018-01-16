<template>
  <div class="accordion" :class="visibilityClasses">
    <button type="button" class="accordion__trigger" @click="onClickVisibility" :aria-expanded="visible ?  'true' : 'false'" >
      <slot name="accordion__title"></slot>
      <div class="accordion__value"><slot name="accordion__value"></slot></div>
      <span v-svg symbol="dropdown_module"></span>
    </button>
    <div class="accordion__dropdown" :aria-hidden="!visible ?  true : null">
      <div class="accordion__list">
        <slot></slot>
      </div>
    </div>
  </div>
</template>

<script>
  import VisibilityMixin from '@/mixins/toggleVisibility'

  export default {
    name: 'A17Accordion',
    mixins: [VisibilityMixin],
    watch: {
      open: function () {
        if (this.visible !== this.open) {
          this.visible = this.open
        }
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .accordion {
    border-bottom:1px solid $color__border--light;
    background-color:$color__background;
    transition: background-color .25s linear;
    overflow:hidden;
  }

  .accordion__trigger {
    @include accordion-trigger;
    color:$color__text;
    display:flex;
    flex-flow: row nowrap;

    &:hover,
    &:focus {
      background:$color__ultralight;
    }

    .icon {
      color:$color__text--light;
    }
  }

  .accordion__value {
    flex-grow:1;
    text-align:right;
    color:$color__text--light;
  }

  .accordion__dropdown {
    max-height:0;
    height:auto;
    overflow:hidden;
    transition: max-height .3s linear, visibility 0s .3s;
    visibility: hidden;
  }

  .accordion__list {
    border-top:1px solid $color__border--light;
    padding:12px 20px;

    .input {
      margin-top:0;

      + .input {
        margin-top:10px;
      }
    }
  }

  .accordion__fields {
    border-top:1px solid $color__border--light;
    padding:20px;
  }

  .accordion__list .accordion__fields {
    border-top:0 none;
    padding:8px 0;
  }

  /* Opened accordion */
  .s--open {
    background-color:$color__ultralight;

    .accordion__dropdown {
      max-height:250px;
      visibility: visible;
      transition: max-height .3s linear;
    }

    .icon {
      transform:rotate(180deg);
    }
  }
</style>
