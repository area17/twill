<template>
  <div class="mediagrid">
    <div class="mediagrid__item" v-show="!item.isReplacement" v-for="(item, index) in itemsLoading" :key="'mediaLoading_' + item.id">
      <span class="mediagrid__button s--loading">
        <span class="mediagrid__progress" v-if="!item.error"><span class="mediagrid__progressBar" :style="loadingProgress(index)"></span></span>
        <span class="mediagrid__progressError" v-else>Upload Error</span>
      </span>
    </div>
    <div class="mediagrid__item" :class="{'s--hasFilename': showFileName}" v-for="item in items" :key="item.id">
      <span class="mediagrid__button" :class="{
          's--picked': isSelected(item),
          's--used': isUsed(item) || !!replacingMediaIds[item.id],
          's--disabled': item.disabled
        }"
        @click.exact="toggleSelection(item)"
        @click.shift.exact="shiftToggleSelection(item)">
          <img :src="item.thumbnail" class="mediagrid__img" />
        </span>
        <p v-if="showFileName" :title="item.name" class="mediagrid__name">{{ item.name }}</p>
    </div>
  </div>
</template>

<script>
  import { mapState } from 'vuex'

  import mediaItemsMixin from '@/mixins/mediaLibrary/mediaItems'

  export default {
    name: 'A17Mediagrid',
    mixins: [mediaItemsMixin],
    computed: {
      ...mapState({
        showFileName: state => state.mediaLibrary.showFileName
      })
    },
    methods: {
      loadingProgress: function (index) {
        return {
          width: this.itemsLoading[index].progress ? this.itemsLoading[index].progress + '%' : '0%'
        }
      }
    }
  }
</script>

<style lang="scss" scoped>
  $height_text: 17px;

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
      width: calc(100% / 2);
      padding-bottom: calc(100% / 2);
    }

    @media (min-width: 600px) {
      width: calc(100% / 2);
      padding-bottom: calc(100% / 2);
    }

    @media (min-width: 800px) {
      width: calc(100% / 3);
      padding-bottom: calc(100% / 3);
    }

    @media (min-width: 1000px) {
      width: calc(100% / 4);
      padding-bottom: calc(100% / 4);
    }

    @media (min-width: 1200px) {
      width: calc(100% / 5);
      padding-bottom: calc(100% / 5);
    }

    @media (min-width: 1400px) {
      width: calc(100% / 6);
      padding-bottom: calc(100% / 6);
    }

    @media (min-width: 1600px) {
      width: calc(100% / 7);
      padding-bottom: calc(100% / 7);
    }

    @media (min-width: 1800px) {
      width: calc(100% / 8);
      padding-bottom: calc(100% / 8);
    }

    @media (min-width: 2000px) {
      width: calc(100% / 9);
      padding-bottom: calc(100% / 9);
    }

    @media (min-width: 2200px) {
      width: calc(100% / 10);
      padding-bottom: calc(100% / 10);
    }

    &.s--hasFilename {
      @media (max-width: 300px) {
        width: 100%;
        padding-bottom: calc(100% + #{$height_text});
      }

      @media (min-width: 300px) {
        padding-bottom: calc((100% / 2) + #{$height_text});
      }

      @media (min-width: 600px) {
        padding-bottom: calc((100% / 2) + #{$height_text});
      }

      @media (min-width: 800px) {
        padding-bottom: calc((100% / 3) + #{$height_text});
      }

      @media (min-width: 1000px) {
        padding-bottom: calc((100% / 4) + #{$height_text});
      }

      @media (min-width: 1200px) {
        padding-bottom: calc((100% / 5) + #{$height_text});
      }

      @media (min-width: 1400px) {
        padding-bottom: calc((100% / 6) + #{$height_text});
      }

      @media (min-width: 1600px) {
        padding-bottom: calc((100% / 7) + #{$height_text});
      }

      @media (min-width: 1800px) {
        padding-bottom: calc((100% / 8) + #{$height_text});
      }

      @media (min-width: 2000px) {
        padding-bottom: calc((100% / 9) + #{$height_text});
      }

      @media (min-width: 2200px) {
        padding-bottom: calc((100% / 10) + #{$height_text});
      }
    }

  }

  .mediagrid__button {
    position: absolute;
    cursor:pointer;

    display: flex;
    justify-content: center; /* align horizontal */
    flex-direction: column;
    align-items: center; /* align vertical */
    @include font-regular;

    user-select: none;
    // background:$color__lighter;

    top: 10px;
    left: 10px;
    right: 10px;
    bottom: 10px;

    .s--hasFilename & {
      bottom: calc(10px + #{$height_text})
    }

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

    &.s--disabled {
      pointer-events: none;
      opacity: 0.2;
    }
  }

  .mediagrid__name {
    position: absolute;
    bottom: 0;
    right: 0;
    left: 0;
    padding: 3px 20px;
    white-space: nowrap;
    text-overflow: ellipsis;
    overflow: hidden;
    font-size: 13px;
    color: $color__text--light;
    width: 100%;
    text-align: center;
    cursor: default;
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
