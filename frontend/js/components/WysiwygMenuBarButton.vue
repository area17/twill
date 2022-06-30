<template>
  <button class="wysiwyg__menubar-button"
          :class="{ 'is-active': isActive }"
          type="button"
          @click="handleClick">
    <span class="icon"
          :class="`icon--wysiwyg_${icon}`"
          aria-hidden="true">
    <svg>
      <title>{{ icon }}</title>
      <use :xlink:href="`#icon--wysiwyg_${icon}`"></use>
    </svg>
  </span>
  </button>
</template>

<script>
  export default {
    name: 'WysiwygMenuBarButton',
    props: {
      icon: {
        type: String,
        required: true
      },
      isActive: {
        type: Boolean,
        default: false
      }
    },
    methods: {
      handleClick () {
        this.$emit('btn:click')
      }
    }
  }
</script>

<style lang="scss" scoped>

  //@import '~svg-spritemap-webpack-plugin/svg-sprite-icons-wysiwyg';

  //@each $name, $icon in $icons-wysiwyg-sizes {
  //  .icon--#{$name},
  //  .icon--#{$name} svg {
  //    width: map_get($icon, 'width');
  //    height: map_get($icon, 'height');
  //  }
  //}
  //
  .wysiwyg__menubar-button {
    width: 24px;
    margin-right: 10px;
    margin-top: 5px;
    margin-bottom: 5px;
    font-size: 1em;
    border: 0 none;
    outline: none;
    cursor: pointer;
    text-align: center;
    background-color: transparent;
    -webkit-appearance: none;

    &:hover,
    &:focus,
    &.is-active {
      color: $color__link;
    }
  }

  .icon {
    width: 15px;
    height: 15px;

    svg {
      width: 15px;
      height: 15px;
    }
  }
</style>
