<template functional>
  <div class="spinner__container">
    <div class="spinner">
      <slot></slot>
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
      text-align: center;r;
    }
  }
</style>
