<template>
  <div class="buttonbar"><slot></slot></div>
</template>

<script>
  export default {
    name: 'A17Buttonbar'
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  $toolbar__height:40px;
  $border__radius:2px;

  .buttonbar {
    font-size:0;
    border:1px solid $color__fborder;
    clear:both;
    overflow:hidden;
    border-radius:2px;
    display:flex;

    > a,
    > button {
      @include btn-reset;
      height:$toolbar__height - 2px;
      line-height:$toolbar__height - 2px;
      display:block;
      float:left;
      padding:0 20px;
      position:relative;
      color:$color__f--text;
      background: $color__background;
      font-size:15px;
      text-decoration:none;
      border-right:1px solid $color__border--light;
      display: flex;
      flex-wrap:no-wrap;
      align-items: center;
      justify-content: center;
      border-radius:0;

      &:first-child {
        border-top-left-radius: $border__radius;
        border-bottom-left-radius: $border__radius;
      }

      &:last-child {
        border-top-right-radius: $border__radius;
        border-bottom-right-radius: $border__radius;
        border-right:0 none;
      }

      .icon {
        color:$color__icons;
        display: block;
      }

      &:hover {
        color:$color__text;
        background:$color__f--bg;

        .icon {
          color:$color__text;
        }
      }
    }

    &:hover {
      border-color: $color__border--focus;
    }
  }

</style>
