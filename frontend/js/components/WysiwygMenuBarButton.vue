<template>
  <button :class="{ 'is-active': isActive, 'wysiwyg__menubar-button': icon || iconUrl }"
          :disabled="disabled"
          type="button"
          :title="label"
          @click="handleClick">
    <template v-if="iconUrl">
      <img class="icon--custom" :src="iconUrl" :alt="label"/>
    </template>
    <template v-else-if="icon">
    <span class="icon"
          :class="`icon--wysiwyg_${icon}`"
          aria-hidden="true">
    <svg>
      <title>{{ icon }}</title>
      <use :xlink:href="`#icon--wysiwyg_${icon}`"></use>
    </svg>
    </span>
    </template>
    <template v-else>
      {{ label }}
    </template>
  </button>
</template>

<script>
  export default {
    name: 'WysiwygMenuBarButton',
    props: {
      icon: {
        type: String,
        required: false
      },
      iconUrl: {
        type: String,
        required: false
      },
      label: {
        type: String,
        required: false,
      },
      isActive: {
        type: Boolean,
        default: false
      },
      disabled: {
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

<style lang="scss">
  .wysiwyg__menubar-button:disabled {
    opacity: 10%;
  }

  .icon--custom {
    width: 14px;
    height: 14px;
  }

  // This icon is not part of the sizes above.
  .icon--wysiwyg_hr,
  .icon--wysiwyg_hr svg {
    width: 14px;
    height: 14px;
  }
</style>

<style lang="scss" scoped>
  @import '~svg-spritemap-webpack-plugin/svg-sprite-icons-wysiwyg';

  @each $name, $icon in $icons-wysiwyg-sizes {
    .icon--#{$name},
    .icon--#{$name} svg {
      width: map_get($icon, 'width');
      height: map_get($icon, 'height');
    }
  }

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
