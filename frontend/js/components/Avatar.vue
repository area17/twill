<template>
  <div :class="avatarClasses">
    <img v-if="thumbnail" :src="thumbnail" :alt="name" />
    <span class="avatar__letter">{{ nameFirstLetter }}</span>
  </div>
</template>

<script>
  export default {
    name: 'A17Avatar',
    props: {
      name: {
        type: String,
        default: null
      },
      thumbnail: {
        type: String,
        default: null
      }
    },
    computed: {
      avatarClasses () {
        return [
          'avatar',
          this.getBackgroundColor
        ]
      },
      getBackgroundColor () {
        // Init colors.
        const colors = ['orange', 'blue', 'purple', 'red']

        // Calculate indexColor.
        const indexColor = (this.name.length % colors.length)

        // Return background class.
        return `avatar--background-${colors[indexColor]}`
      },
      nameFirstLetter () {
        return this.name.charAt(0)
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .avatar {
    position: relative;
    width: 36px;
    height: 36px;
    border-radius: 50%;

    img {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      min-height: 0;
      object-fit: cover;
      border-radius: 50%;
      z-index: 2;
    }
  }

  .avatar__letter {
    position: absolute;
    top: 1px;
    left: 1px;
    display: flex;
    flex-flow: row wrap;
    align-items: center;
    justify-content: center;
    width: calc(100% - 2px);
    height: calc(100% - 2px);
    color: $color__background;
    border-radius: 50%;
    z-index: 1;
  }

  /* Modifiers */

  .avatar--background-orange {
    .avatar__letter {
      background-color: $color__user-orange;
    }
  }

  .avatar--background-blue {
    .avatar__letter {
      background-color: $color__user-blue;
    }
  }

  .avatar--background-purple {
    .avatar__letter {
      background-color: $color__user-purple;
    }
  }

  .avatar--background-red {
    .avatar__letter {
      background-color: $color__user-red;
    }
  }
</style>
