<template functional>
  <div class="spinner__container">
    <div class="spinner">
      <div class="dot1"></div>
      <div class="dot2"></div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .spinner__container {
    display: flex;
    width: 100%;
    // height: 50px;
    padding: 10vh 0;
    background-color: rgba($color__background, 0.5);
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: $zindex__loadingTable;

    .spinner {
      margin: 100px auto;
      width: 20px;
      height: 20px;
      position: relative;
      text-align: center;

      animation: sk-rotate 2.0s infinite linear;

      .dot1,
      .dot2 {
        width: 60%;
        height: 60%;
        display: inline-block;
        position: absolute;
        top: 0;
        background-color: $color__text;
        border-radius: 100%;

        animation: sk-bounce 2.0s infinite ease-in-out;
      }

      .dot2 {
        top: auto;
        bottom: 0;
        animation-delay: -1.0s;
      }
    }

    @keyframes sk-rotate {
      100% {
        transform: rotate(360deg);
      }
    }

    @keyframes sk-bounce {
      0%, 100% {
        transform: scale(0.0);
      }
      50% {
        transform: scale(1.0);
      }
    }
  }
</style>
