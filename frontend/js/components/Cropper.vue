<template>
  <div class="cropper">
    <header class="cropper__header">
      <ul v-if="multiCrops" class="cropper__breakpoints">
        <li v-for="(crop, key, index) in currentMedia.crops" :class="{ 's--active' : toggleBreakpoint === index }" @click="changeCrop(key, index)">{{ key | capitalize }}</li>
      </ul>
    </header>
    <div class="cropper__content">
      <div class="cropper__wrapper" ref="cropWrapper">
        <img class="cropper__img" ref="cropImage" :src="currentMedia.crop || currentMedia.original" :alt="currentMedia.name">
      </div>
    </div>
    <footer class="cropper__footer">
      <slot></slot>
      <ul v-if="ratiosByContext.length > 1" class="cropper__ratios">
        <li v-for="ratio in ratiosByContext" @click="changeRatio(ratio)" :key="ratio.name" :class="{ 's--active' : currentRatioName === ratio.name }">{{ ratio.name | capitalize }}</li>
      </ul>
      <span class="cropper__values f--small" :class="cropperWarning">{{ cropValues.originalWidth }} &times; {{ cropValues.originalHeight }}</span>
    </footer>
  </div>
</template>

<script>
  import { mapState } from 'vuex'

  import a17VueFilters from '@/utils/filters.js'
  import CropperJs from 'cropperjs'
  import 'cropperjs/dist/cropper.min.css'
  import cropperMixin from '@/mixins/cropper'

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
          width: this.media.crops[Object.keys(this.media.crops)[0]].width,
          height: this.media.crops[Object.keys(this.media.crops)[0]].height,
          originalWidth: this.media.width,
          originalHeight: this.media.height,
          naturalWidth: null,
          naturalHeight: null
        },
        minCropValues: {
          width: 0,
          height: 0
        },
        currentRatioName: this.media.crops[Object.keys(this.media.crops)[0]].name
      }
    },
    computed: {
      crop: function () {
        return this.currentMedia.crops[this.currentCrop]
      },
      multiCrops: function () {
        return Object.keys(this.media.crops).length > 1
      },
      ratiosByContext: function () {
        const filtered = this.allCrops[this.context][this.currentCrop]
        if (filtered) {
          return filtered
        }
        return []
      },
      initAspectRatio: function () {
        let self = this
        let filtered = self.ratiosByContext
        let filter = filtered.find(function (r) {
          return r.name === self.currentRatioName
        })
        if (typeof filter !== 'undefined' && filter) {
          self.minCropValues.width = filter.minValues ? filter.minValues.width : 0
          self.minCropValues.height = filter.minValues ? filter.minValues.height : 0
          return filter.ratio
        }
        return self.aspectRatio
      },
      cropperOpts: function () {
        let self = this
        return {
          ...this.defaultCropsOpts,
          data: this.crop,
          cropmove: function () {
            self.updateCropperValues()
          },
          cropend: function () {
            self.sendCropperValues()
          }
        }
      },
      cropperWarning: function () {
        return {
          'cropper__warning': this.cropValues.originalWidth < this.minCropValues.width || this.cropValues.originalHeight < this.minCropValues.height
        }
      },
      ...mapState({
        allCrops: state => state.mediaLibrary.crops
      })
    },
    filters: a17VueFilters,
    mounted: function () {
      let self = this
      let opts = self.cropperOpts
      let imageBox = self.$refs.cropImage
      let imageWrapper = self.$refs.cropWrapper
      let img = new Image()
      img.onload = function () {
        imageWrapper.style.maxWidth = imageWrapper.getBoundingClientRect().width + 'px'
        imageWrapper.style.minHeight = imageWrapper.getBoundingClientRect().height + 'px'

        self.cropper = new CropperJs(imageBox, opts)
      }

      img.src = self.currentMedia.crop || self.currentMedia.original

      // init displayed crop values
      imageBox.addEventListener('ready', function () {
        self.cropValues.naturalWidth = img.naturalWidth
        self.cropValues.naturalHeight = img.naturalHeight
        self.updateCropperValues()
        self.sendCropperValues()
      })
    },
    methods: {
      changeCrop: function (cropName, index) {
        this.currentCrop = cropName

        let ratio = this.initAspectRatio
        this.cropper.setAspectRatio(ratio)
        this.cropper.setData(this.crop)

        this.toggleBreakpoint = index

        this.updateCropperValues()
        this.sendCropperValues()
      },
      changeRatio: function (ratioObj) {
        this.currentRatioName = ratioObj.name
        this.cropper.setAspectRatio(ratioObj.ratio)
        this.minCropValues.width = ratioObj.minValues ? ratioObj.minValues.width : 0
        this.minCropValues.height = ratioObj.minValues ? ratioObj.minValues.height : 0

        this.sendCropperValues()
        this.updateCropperValues()
      },
      updateCropperValues: function () {
        let data = this.cropper.getData(true)
        this.setCropperValues(data)
      },
      setCropperValues: function (data) {
        this.cropValues.width = data.width
        this.cropValues.height = data.height
        this.cropValues.originalWidth = Math.round(this.cropValues.width * this.currentMedia.width / this.cropValues.naturalWidth)
        this.cropValues.originalHeight = Math.round(this.cropValues.height * this.currentMedia.height / this.cropValues.naturalHeight)
      },
      sendCropperValues: function () {
        let data = {}
        data.values = {}
        data.values[this.currentCrop] = this.cropper.getData(true)
        data.values[this.currentCrop].name = this.currentRatioName
        this.$emit('crop-end', data)
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

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
      border-radius: $height_li / 2;

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
    display: flex;
    justify-content: center;
    align-items: center;
    min-height:75px;

    .cropper__ratios {
      padding: 20px 0;

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
