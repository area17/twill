<template>
  <div class="mediagrid">
    <div class="mediagrid__item" v-for="(item, index) in itemsLoading" :key="'mediaLoading_' + item.id">
      <span class="mediagrid__button s--loading">
        <span class="mediagrid__progress" v-if="!item.error"><span class="mediagrid__progressBar" :style="loadingProgress(index)"></span></span>
        <span class="mediagrid__progressError" v-else>Upload Error</span>
      </span>
    </div>
    <div class="mediagrid__item" v-for="item in items" :key="item.id">
      <span class="mediagrid__button" :class="{ 's--picked': isSelected(item), 's--used': isUsed(item) }" @click.exact="toggleSelection(item)" @click.shift.exact="shiftToggleSelection(item)"><img :src="item.thumbnail" class="mediagrid__img" /></span>
    </div>
  </div>
</template>

<script>
  import mediaItemsMixin from '@/mixins/mediaLibrary/mediaItems'

  export default {
    name: 'A17Mediagrid',
    mixins: [mediaItemsMixin],
    methods: {
      loadingProgress: function (index) {
        return {
          'width': this.itemsLoading[index].progress ? this.itemsLoading[index].progress + '%' : '0%'
        }
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .mediagrid {
    display: block;
    width: 100%;
    height: 100%;
    font-size:0;
    line-height: 1em;
  }

  .mediagrid__item {
    position:relative;
    display:inline-block;
    width: 16.66666665%;
    padding-bottom: 16.66666665%;
    overflow: hidden;
    background:white;

    @media (max-width: 300px) {
      width: 100%;
      padding-bottom: 100%;
    }

    @media (min-width: 300px) {
      width: (100% / 2);
      padding-bottom: (100% / 2);
    }

    @media (min-width: 600px) {
      width: (100% / 2);
      padding-bottom: (100% / 2);
    }

    @media (min-width: 800px) {
      width: (100% / 3);
      padding-bottom: (100% / 3);
    }

    @media (min-width: 1000px) {
      width: (100% / 4);
      padding-bottom: (100% / 4);
    }

    @media (min-width: 1200px) {
      width: (100% / 5);
      padding-bottom: (100% / 5);
    }

    @media (min-width: 1400px) {
      width: (100% / 6);
      padding-bottom: (100% / 6);
    }

    @media (min-width: 1600px) {
      width: (100% / 7);
      padding-bottom: (100% / 7);
    }

    @media (min-width: 1800px) {
      width: (100% / 8);
      padding-bottom: (100% / 8);
    }

    @media (min-width: 2000px) {
      width: (100% / 9);
      padding-bottom: (100% / 9);
    }

    @media (min-width: 2200px) {
      width: (100% / 10);
      padding-bottom: (100% / 10);
    }
  }

  .mediagrid__button {
    position: absolute;
    cursor:pointer;

    display: flex;
    justify-content: center; /* align horizontal */
    align-items: center; /* align vertical */
    @include font-regular;

    user-select: none;
    background:$color__lighter;

    top: 10px;
    left: 10px;
    right: 10px;
    bottom: 10px;

    &:before {
      content: "";
      position: absolute;
      display:block;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      border:1px solid rgba(0,0,0,0.05);
    }

    &.s--picked {
      &:after {
        content: "";
        position: absolute;
        display:block;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        border:4px solid $color__link;
        z-index: 1;
      }
    }

    &.s--used {
      &:before {
        content: "";
        position: absolute;
        display:block;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: $color__translucentBlue;
        opacity: 0.85;
      }
    }
  }

  .s--loading {
    background:$color__f--bg;
    cursor:default;
  }

  .mediagrid__img {
    display:block;
    max-width:100%;
    height:auto;
    max-height:100%;
  }

  .mediagrid__progress {
    height: 4px;
    width: 80%;
    background: $color__border--focus;
    border-radius: 2px;
    position: relative;
  }

  .mediagrid__progressBar {
    position: absolute;
    top:0;
    left:0;
    width: 100%;
    border-radius: 2px;
    height:4px;
    background: $color__action;
  }

  .mediagrid__progressError {
    color:$color__error;
  }
</style>
