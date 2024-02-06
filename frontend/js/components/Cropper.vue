<template>
  <div class="cropper">
    <header class="cropper__header">
      <ul v-if="multiCrops" class="cropper__breakpoints">
        <li v-for="(crop, key, index) in cropOptions" :key="key" :class="{ 's--active' : toggleBreakpoint === index }" @click="changeCrop(key, index)">{{ key | capitalize }}</li>
      </ul>
    </header>
    <div class="cropper__content">
      <div class="cropper__wrapper" ref="cropWrapper">
        <img class="cropper__img" ref="cropImage" :src="currentMedia.medium || currentMedia.original" :alt="currentMedia.name">
      </div>
    </div>
    <footer class="cropper__footer">
      <ul v-if="ratiosByContext.length > 1" class="cropper__ratios">
        <li class="f--small" v-for="ratio in ratiosByContext" @click="changeRatio(ratio)" :key="ratio.name" :class="{ 's--active' : currentRatioName === ratio.name }">{{ ratio.name | capitalize }}</li>
      </ul>
      <span class="cropper__values f--small hide--xsmall" :class="cropperWarning">{{ cropValues.original.width }} &times; {{ cropValues.original.height }}</span>
      <slot></slot>
    </footer>
  </div>
</template>

<script>
  import 'cropperjs/dist/cropper.min.css'

  import CropperJs from 'cropperjs'
  import { mapState } from 'vuex'

  import cropperMixin from '@/mixins/cropper'
  import { cropConversion } from '@/utils/cropper'
  import a17VueFilters from '@/utils/filters.js'

  export default {
    name: 'a17Cropper',
    props: {
      media: {
        type: Object,
        default: () => {}
      },
      context: {
        type: String,
        default: '' // listing, cover etc...
      }
    },
    mixins: [cropperMixin],
    data: function () {
      return {
        cropper: null,
        currentMedia: this.media,
        currentCrop: Object.keys(this.media.crops)[0],
        toggleBreakpoint: 0,
        cropValues: {
          natural: {
            width: null,
            height: null
          },
          original: {
            width: this.media.crops[Object.keys(this.media.crops)[0]].width,
            height: this.media.crops[Object.keys(this.media.crops)[0]].height
          }
        },
        minCropValues: {
          width: 0,
          height: 0
        },
        currentRatioName: this.media.crops[Object.keys(this.media.crops)[0]].name
      }
    },
    watch: {
      media: function (newMedia) {
        this.currentMedia = newMedia
      }
    },
    computed: {
      cropOptions: function () {
        if (this.allCrops.hasOwnProperty(this.context)) return this.allCrops[this.context]
        return {}
      },
      crop: function () {
        return this.currentMedia.crops[this.currentCrop] || {}
      },
      multiCrops: function () {
        return Object.keys(this.cropOptions).length > 1
      },
      ratiosByContext: function () {
        const filtered = this.cropOptions[this.currentCrop]
        if (filtered) {
          return filtered
        }
        return []
      },
      cropperOpts: function () {
        return {
          ...this.defaultCropsOpts,
          cropmove: () => {
            this.updateCropperValues()
          },
          cropend: () => {
            this.sendCropperValues()
          }
        }
      },
      cropperWarning: function () {
        return {
          cropper__warning: this.cropValues.original.width < this.minCropValues.width || this.cropValues.original.height < this.minCropValues.height
        }
      },
      ...mapState({
        allCrops: state => state.mediaLibrary.crops
      })
    },
    filters: a17VueFilters,
    mounted: function () {
      const opts = this.cropperOpts
      const imageBox = this.$refs.cropImage
      const imageWrapper = this.$refs.cropWrapper
      const img = new Image()

      img.addEventListener('load', () => {
        imageWrapper.style.maxWidth = imageWrapper.getBoundingClientRect().width + 'px'
        imageWrapper.style.minHeight = imageWrapper.getBoundingClientRect().height + 'px'

        this.cropper = new CropperJs(imageBox, opts)
      }, {
        once: true,
        passive: true,
        capture: true
      })

      img.src = this.currentMedia.medium || this.currentMedia.original

      // init displayed crop values
      imageBox.addEventListener('ready', () => {
        this.cropValues.natural.width = img.naturalWidth
        this.cropValues.natural.height = img.naturalHeight
        this.updateCrop()
      }, {
        once: true,
        passive: true,
        capture: true
      })
    },
    methods: {
      initAspectRatio: function () {
        const filtered = this.ratiosByContext
        const filter = filtered.find((r) => r.name === this.currentRatioName)

        if (typeof filter !== 'undefined' && filter) {
          this.minCropValues.width = filter.minValues ? filter.minValues.width : 0
          this.minCropValues.height = filter.minValues ? filter.minValues.height : 0
          this.cropper.setAspectRatio(filter.ratio)
          return
        }
        this.cropper.setAspectRatio(this.aspectRatio)
      },
      changeCrop: function (cropName, index) {
        this.currentCrop = cropName
        // If the current crop doesn't exist on the current media, the cropper will
        // be set at the center of the image, using the first available ratio.
        this.currentRatioName = this.crop.name || this.cropOptions[cropName][0].name
        this.toggleBreakpoint = index

        this.updateCrop()
        this.sendCropperValues()
      },
      changeRatio: function (ratioObj) {
        this.currentRatioName = ratioObj.name
        this.updateCrop()
        this.sendCropperValues()
      },
      updateCrop: function () {
        this.initAspectRatio()
        this.initCrop()
        this.updateCropperValues()
      },
      updateCropperValues: function () {
        const data = this.cropper.getData(true)
        const originalCrop = this.toOriginalCrop(data)
        this.cropValues.original.width = originalCrop.width
        this.cropValues.original.height = originalCrop.height
      },
      initCrop: function () {
        const crop = this.toNaturalCrop(this.crop)
        // Mike (mike@area17.com) --
        //
        // it seems due to rounding errors(?) that sometimes
        // the x position can be reset incorrectly
        // see: https://github.com/fengyuanchen/cropperjs/issues/1057
        //
        // from my testing it seems to be a little inconsistent and unpredictable
        // I guess you just need for the rounding error to happen
        // But, it seems setting the properties individually avoids this...
        //
        // -- Mike (mike@area17.com)
        this.cropper.setData({ x: crop.x })
        this.cropper.setData({ y: crop.y })
        this.cropper.setData({ width: crop.width })
        this.cropper.setData({ height: crop.height })
      },
      test: function () {
        const crop = this.toNaturalCrop({ x: 0, y: 0, width: 380, height: 475 })
        this.cropper.setAspectRatio(0.8)
        this.cropper.setData(crop)
      },
      sendCropperValues: function () {
        const data = {}
        data.values = {}
        data.values[this.currentCrop] = this.toOriginalCrop(this.cropper.getData(true))
        data.values[this.currentCrop].name = this.currentRatioName

        this.$emit('crop-end', data)
      },
      toNaturalCrop: function (data) {
        return cropConversion(data, this.cropValues.natural, this.currentMedia)
      },
      toOriginalCrop: function (data) {
        return cropConversion(data, this.currentMedia, this.cropValues.natural)
      }
    },
    beforeDestroy: function () {
      this.cropper.destroy()
    }
  }
</script>

<style lang="scss" scoped>

  $height_li: 35px;

  .cropper {
    width: 100%;
    display: flex;
    flex-flow: column nowrap;
  }

  .cropper__content {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-grow:1;
    // display: block;
    height: 430px;
    background-color: $color__light;

    //override cropper.js style
    .cropper-modal {
      background-color: $color__light;
    }

  }

  .cropper__wrapper {
    display: block;
    height: 100%;
    margin: 0 auto;
  }

  .cropper__img {
    display: block;
    max-width: 100%;
    height: 100%;
    margin: 0 auto;
    opacity: 0;
  }

  .cropper__breakpoints {
    padding: 20px 0;

    li {
      display: inline-block;
      height: $height_li;
      line-height: $height_li;
      background-color: $color__background;
      color: $color__link;
      cursor: pointer;
      margin: 0 20px;
      border-radius: calc($height_li / 2);

      &.s--active {
        color: $color__text;
        background-color: $color__light;
        cursor: default;
        padding: 0 20px;
        margin: 0;
      }

      &:not(.s--active):hover {
        text-decoration: underline;
      }

      &:last-child {
        margin-right: 0;
      }
    }
  }

  .cropper__footer {
    position: relative;
    width: 100%;

    @include breakpoint('small+') {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height:75px;
    }

    .cropper__ratios {
      padding: 20px 0 0 0;
      text-align: center;

      @include breakpoint('small+') {
        padding: 20px 0;
      }

      li {
        @include font-smoothing();

        display: inline-block;
        height: $height_li;
        line-height: $height_li - 2px;
        margin-right: 15px;
        padding: 0 20px;
        background-color: transparent;
        border: 1px solid $color__border--hover;
        border-radius: 5px;
        color: $color__text--light;
        cursor: pointer;

        &:hover,
        &.s--active {
          border-color: $color__text;
          color: $color__text;
        }

        &:focus {
          border-color: $color__text;
          color: $color__text;
        }

        &:disabled {
          opacity: .5;
          pointer-events: none;
        }

        &:last-child {
          margin-right: 0;
        }

        &.s--active {
          cursor: default;
        }
      }
    }

    .cropper__values {
      @include font-smoothing();

      position: absolute;
      top: 50%;
      right: 0;
      color: $color__ok;
      height: $height_li;
      line-height: $height_li;
      transform: translateY(-50%);
      transition: color 250ms ease;

      &.cropper__warning {
        color: $color__error;
      }
    }
  }
</style>
