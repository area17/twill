<template>
  <div class="media" :class="{ 'media--hoverable' : hover, 'media--slide' : isSlide }">
    <div class="media__field">
      <div class="media__info" v-if="hasMedia">
        <div class="media__img">
          <div class="media__imgFrame">
            <div class="media__imgCentered" :class="cropThumbnailClass" :style="cropThumbnailStyle">
              <img v-if="cropSrc || showImg" :src="cropSrc" crossorigin="anonymous" ref="mediaImg" :class="cropThumbnailClass"/>
            </div>
            <div class="media__edit" @click="openMediaLibrary(1, mediaKey, index)">
              <span class="media__edit--button"><span v-svg symbol="edit"></span></span>
            </div>
          </div>
        </div>

        <ul class="media__metadatas">
          <li class="media__name" @click="openMediaLibrary(1, mediaKey, index)"><strong :title="media.name">{{
            media.name }}</strong></li>
          <li class="f--small" v-if="media.size">File size: {{ media.size | uppercase }}</li>
          <li class="f--small" v-if="media.width + media.height">Original: {{ media.width }}&nbsp;&times;&nbsp;{{
            media.height }}
          </li>
          <li class="f--small media__crop-link" v-if="cropInfos" @click="openCropMedia">
            <div class="media__crop-link-col">
              <span class="f--small f--note hide--xsmall">Cropped:&nbsp;</span>
            </div>
            <div class="media__crop-link-col">
              <span class="f--small f--note hide--xsmall" v-html="cropInfos"></span>
            </div>
          </li>
          <li class="f--small">
            <a href="#" @click.prevent="metadatasInfos" v-if="withAddInfo" class="f--link-underlined--o">{{ metadatas.text }}</a>
          </li>
        </ul>

        <!--Actions-->
        <a17-buttonbar class="media__actions">
          <a :href="media.original" download><span v-svg symbol="download"></span></a>
          <button type="button" @click="openCropMedia" v-if="activeCrop"><span v-svg symbol="crop"></span></button>
          <button type="button" @click="deleteMediaClick"><span v-svg symbol="trash"></span></button>
        </a17-buttonbar>

        <div class="media__actions-dropDown">
          <a17-dropdown ref="dropDown" position="right">
            <a17-button size="icon" variant="icon" @click="$refs.dropDown.toggle()">
              <span v-svg symbol="more-dots"></span></a17-button>
            <div slot="dropdown__content">
              <a :href="media.original" download><span v-svg symbol="download"></span> Download</a>
              <button type="button" @click="openCropMedia" v-if="activeCrop"><span v-svg symbol="crop"></span> Crop
              </button>
              <button type="button" @click="deleteMediaClick"><span v-svg symbol="trash"></span> Delete</button>
            </div>
          </a17-dropdown>
        </div>
      </div>

      <!--Add media button-->
      <a17-button variant="ghost" @click="openMediaLibrary" :disabled="disabled" v-if="!hasMedia">{{ btnLabel }}</a17-button>
      <p class="media__note f--small" v-if="!!this.$slots.default">
        <slot/>
      </p>

      <!-- Metadatas options -->
      <div class="media__metadatas--options" :class="{ 's--active' : metadatas.active }" v-if="hasMedia">
        <a17-mediametadata :name='metadataName' label="Alt Text" id="altText" :media="media" @change="updateMetadata"/>
        <a17-mediametadata v-if="withCaption" :name='metadataName' label="Caption" id="caption" :media="media" @change="updateMetadata"/>
        <a17-mediametadata v-if="withVideoUrl" :name='metadataName' label="Video URL (optional)" id="video" :media="media" @change="updateMetadata"/>
      </div>
    </div>

    <!-- Crop modal -->
    <a17-modal class="modal--cropper" :ref="cropModalName" :forceClose="true" title="Edit image crop" mode="medium" v-if="hasMedia && activeCrop">
      <a17-cropper :media="media" v-on:crop-end="cropMedia" :aspectRatio="16 / 9" :context="cropContext" :key="cropperKey">
        <a17-button class="cropper__button" variant="action" @click="$refs[cropModalName].close()">Update</a17-button>
      </a17-cropper>
    </a17-modal>
  </div>
</template>

<script>
  import { mapState } from 'vuex'

  import { MEDIA_LIBRARY } from '@/store/mutations'

  import a17Cropper from '@/components/Cropper.vue'
  import a17MediaMetadata from '@/components/MediaMetadata.vue'
  import mediaLibrayMixin from '@/mixins/mediaLibrary/mediaLibrary.js'
  import a17VueFilters from '@/utils/filters.js'
  import { cropConversion } from '@/utils/cropper'
  import smartCrop from 'smartcrop'

  export default {
    name: 'A17Mediafield',
    components: {
      'a17-cropper': a17Cropper,
      'a17-mediametadata': a17MediaMetadata
    },
    mixins: [mediaLibrayMixin],
    props: {
      name: {
        type: String,
        required: true
      },
      disabled: {
        type: Boolean,
        default: false
      },
      required: {
        type: Boolean,
        default: false
      },
      btnLabel: {
        type: String,
        default: 'Attach image'
      },
      hover: {
        type: Boolean,
        default: false
      },
      isSlide: {
        type: Boolean,
        default: false
      },
      // Index of media in selected context
      index: {
        type: Number,
        default: 0
      },
      // current media context put in store. eg: slideshow, cover...
      mediaContext: {
        type: String,
        default: ''
      },
      // current crop context put in store. eg: slideshow, cover...
      cropContext: {
        type: String,
        default: ''
      },
      withAddInfo: {
        type: Boolean,
        default: true
      },
      withVideoUrl: {
        type: Boolean,
        default: true
      },
      withCaption: {
        type: Boolean,
        default: true
      },
      activeCrop: {
        type: Boolean,
        default: true
      }
    },
    data: function () {
      return {
        canvas: null,
        img: null,
        ctx: null,
        isDataToUrl: false,
        imgLoaded: false,
        cropSrc: false,
        showImg: false,
        isDestroyed: false,
        naturalDim: {
          width: null,
          height: null
        },
        originalDim: {
          width: null,
          height: null
        },
        hasMediaChange: false,
        metadatas: {
          text: 'Edit info',
          textOpen: 'Edit info',
          textClose: 'Close info',
          active: false
        }
      }
    },
    filters: a17VueFilters,
    computed: {
      cropThumbnailStyle: function () {
        if (!this.hasMedia) return {}
        if (!this.media.crops) return {}
        if (!this.cropSrc) return {}

        return {
          'backgroundImage': `url(${this.cropSrc})`
        }
      },
      cropThumbnailClass: function () {
        if (!this.hasMedia) return {}
        if (!this.media.crops) return {}
        if (!this.isDataToUrl) return {}
        const crop = this.media.crops[Object.keys(this.media.crops)[0]]
        return {
          'media__img--landscape': crop.width / crop.height >= 1,
          'media__img--portrait': crop.width / crop.height < 1
        }
      },
      mediaKey: function () {
        return this.mediaContext.length > 0 ? this.mediaContext : this.name
      },
      metadataName: function () {
        return 'mediaMeta[' + this.name + '][' + this.media.id + ']'
      },
      media: function () {
        if (this.selectedMedias.hasOwnProperty(this.mediaKey)) {
          return this.selectedMedias[this.mediaKey][this.index] || {}
        } else {
          return {}
        }
      },
      cropInfos: function () {
        let cropInfos = ''
        let index = 0
        if (this.media.crops) {
          for (let variant in this.media.crops) {
            if (index > 0) {
              cropInfos += ', '
            }
            cropInfos += this.media.crops[variant].width + 'x' + this.media.crops[variant].height + '&nbsp;'
            cropInfos += '(' + this.media.crops[variant].name + ')'
            index++
          }
        }
        return cropInfos.length > 0 ? cropInfos : null
      },
      hasMedia: function () {
        return Object.keys(this.media).length > 0
      },
      cropperKey: function () {
        return `${this.mediaKey}-${this.index}_${this.cropContext}`
      },
      mediaHasCrop: function () {
        return this.media.crops
      },
      cropModalName: function () {
        return `${name}Modal`
      },
      ...mapState({
        selectedMedias: state => state.mediaLibrary.selected,
        allCrops: state => state.mediaLibrary.crops
      })
    },
    watch: {
      media: function (val, oldVal) {
        this.hasMediaChange = val !== oldVal

        if (this.selectedMedias.hasOwnProperty(this.mediaKey)) {
          // reset isDestroyed status because we changed the media
          if (this.selectedMedias[this.mediaKey][this.index]) this.isDestroyed = false
        }
      }
    },
    methods: {
      // crop
      canvasCrop () {
        let crop = this.media.crops[Object.keys(this.media.crops)[0]]
        if (!crop) return

        crop = cropConversion(crop, this.naturalDim, this.originalDim)

        const cropWidth = crop.width
        const cropHeight = crop.height
        this.canvas.width = cropWidth
        this.canvas.height = cropHeight
        this.ctx.drawImage(this.img, crop.x, crop.y, cropWidth, cropHeight, 0, 0, cropWidth, cropHeight)
        this.$nextTick(() => {
          let src = ''
          try {
            src = this.canvas.toDataURL('image/png')
            this.isDataToUrl = true
          } catch (e) {
            console.error(`an error is occured: ${e}`)
            this.isDataToUrl = false
            src = this.media.thumbnail
          }

          if (this.cropSrc !== src) {
            this.cropSrc = src
          }
        })
      },
      setDefaultCrops: function () {
        let defaultCrops = {}
        let smarcrops = []
        if (this.allCrops.hasOwnProperty(this.cropContext)) {
          for (let cropVariant in this.allCrops[this.cropContext]) {
            const ratio = this.allCrops[this.cropContext][cropVariant][0].ratio
            const width = this.media.width
            const height = this.media.height
            const center = {
              x: width / 2,
              y: height / 2
            }

            let cropWidth = width
            let cropHeight = height

            if (ratio > 0 && ratio < 1) { // "portrait" crop
              cropWidth = Math.floor(Math.min(height * ratio, width))
              cropHeight = Math.floor(cropWidth / ratio)
            } else if (ratio >= 1) { // "landscape" or square crop
              cropHeight = Math.floor(Math.min(width / ratio, height))
              cropWidth = Math.floor(cropHeight * ratio)
            }

            let crop = {
              x: 0,
              y: 0,
              width: cropWidth,
              height: cropHeight
            }

            // Convert crop for original img values
            crop = cropConversion(crop, this.naturalDim, this.originalDim)

            smarcrops.push(smartCrop.crop(this.img, {width: crop.width, height: crop.height, minScale: 1.0}))

            let x = Math.floor(center.x - cropWidth / 2)
            let y = Math.floor(center.y - cropHeight / 2)

            defaultCrops[cropVariant] = {}
            defaultCrops[cropVariant].name = this.allCrops[this.cropContext][cropVariant][0].name || cropVariant
            defaultCrops[cropVariant].x = x
            defaultCrops[cropVariant].y = y
            defaultCrops[cropVariant].width = cropWidth
            defaultCrops[cropVariant].height = cropHeight
          }

          Promise.all(smarcrops).then((values) => {
            let index = 0
            values.forEach((value) => {
              const topCrop = {
                x: value.topCrop.x,
                y: value.topCrop.y,
                width: value.topCrop.width,
                height: value.topCrop.height
              }
              // Restore crop natural values (aka: value to store)
              const cropVariant = defaultCrops[Object.keys(defaultCrops)[index]]
              const crop = cropConversion(topCrop, this.originalDim, this.naturalDim)
              cropVariant.x = crop.x
              cropVariant.y = crop.y
              cropVariant.width = crop.width
              cropVariant.height = crop.height
              index++
            })
            this.cropMedia({values: defaultCrops})
          }, (error) => {
            console.error(`An error is occured: ${error}`)
            this.cropMedia({values: defaultCrops})
          })
        } else {
          this.cropMedia({values: defaultCrops})
        }
      },
      cropMedia: function (crop) {
        crop.key = this.mediaKey
        crop.index = this.index
        this.$store.commit(MEDIA_LIBRARY.SET_MEDIA_CROP, crop)
        if (this.img) this.canvasCrop()
      },
      init: function () {
        if (this.hasMedia) {
          this.initImg().then(() => {
            this.naturalDim.width = this.img.naturalWidth
            this.naturalDim.height = this.img.naturalHeight
            this.originalDim.width = this.media.width
            this.originalDim.height = this.media.height

            if (!this.mediaHasCrop) {
              this.setDefaultCrops()
            } else {
              this.canvasCrop()
            }
          }, (e) => {
            console.error(`An error is occured: ${e}`)
            this.showImg = true
            this.originalDim.width = this.media.width
            this.originalDim.height = this.media.height

            this.$nextTick(() => {
              this.$refs.mediaImg.addEventListener('load', () => {
                this.img = this.$refs.mediaImg
                this.naturalDim.width = this.img.naturalWidth
                this.naturalDim.height = this.img.naturalHeight

                if (!this.mediaHasCrop) {
                  this.setDefaultCrops()
                } else {
                  this.canvasCrop()
                }
              }, {
                once: true,
                passive: true,
                capture: true
              })

              this.$refs.mediaImg.onError = (error) => {
                console.error(`An error is occured: ${error}`)
              }

              this.cropSrc = this.media.thumbnail
            })
          })
          this.hasMediaChange = false
        }
      },
      initImg: function () {
        return new Promise((resolve, reject) => {
          this.img = new Image()
          this.img.crossOrigin = 'Anonymous'
          this.canvas = document.createElement('canvas')
          this.ctx = this.canvas.getContext('2d')

          this.img.addEventListener('load', () => {
            resolve()
          }, {
            once: true,
            passive: true,
            capture: true
          })

          this.img.onError = (e) => {
            reject(e)
          }

          this.img.src = this.media.thumbnail
        })
      },
      openCropMedia: function () {
        this.$refs[this.cropModalName].open()
      },
      deleteMediaClick: function () {
        this.isDestroyed = true
        this.deleteMedia()
      },
      // delete the media
      deleteMedia: function () {
        this.$store.commit(MEDIA_LIBRARY.DESTROY_SPECIFIC_MEDIA, {
          name: this.mediaKey,
          index: this.index
        })
      },
      // metadatas
      updateMetadata: function (newValue) {
        this.$store.commit(MEDIA_LIBRARY.SET_MEDIA_METADATAS, {
          media: {
            context: this.mediaKey,
            index: this.index
          },
          value: newValue
        })
      },
      metadatasInfos: function () {
        this.metadatas.active = !this.metadatas.active
        this.metadatas.text = this.metadatas.active ? this.metadatas.textClose : this.metadatas.textOpen
      }
    },
    beforeMount: function () {
      this.init()
    },
    beforeUpdate: function () {
      if (this.hasMediaChange) this.init()
    },
    beforeDestroy: function () {
      if (this.isSlide) return // for Slideshows : the medias are deleted when the slideshow component is destroyed (so no need to do it here)
      if (!this.isDestroyed) this.deleteMedia()
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  $input-bg: #FCFCFC;
  $input-border: #DFDFDF;
  $height_input: 45px;

  .media {
    border-radius: 2px;
    border: 1px solid $color__border;
    background:$color__background;
  }

  .media__field {
    // height:$height_input + 2px;
    padding: 10px;
    position: relative;
    /*overflow-x: hidden;*/
  }

  .media--slide {
    border: 0 none;
  }

  .media__note {
    color: $color__text--light;
    float: right;
    position: absolute;
    bottom: 18px;
    right: 15px;
    display:none;

    @include breakpoint('small+') {
      display: inline-block;
    }

    @include breakpoint('medium') {
      display: none;
    }

    .s--in-editor & {
      @include breakpoint('small+') {
        display: none;
      }
    }
  }

  .media__img {
    width: 33.33%;
    max-width: 240px;
    user-select: none;
    position:relative;
    min-width: 100px;

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

    img {
      display:block;
      max-width:100%;
      max-height:100%;
      opacity: 0;
      visibility: hidden;

      &.media__img--landscape {
        width: 100%;
        height: auto;
      }

      &.media__img--portrait {
        width: auto;
        height: 100%;
      }
    }
  }

  .media--slide .media__img {
    max-width: 120px;
  }

  .media__crop-link {
    display: flex;
    flex-direction: row;
    text-decoration: none;
    cursor: pointer;

    .media__crop-link-col {
      display: inline-block;
    }

    &:hover .f--small {
      @include bordered($color__text, false);
    }

    @include breakpoint('medium-') {
      flex-direction: column;
    }
  }

  // .media__square {
  //   width:100%;
  // }

  // Image centered in a square option
  .media__imgFrame {
    width:100%;
    padding-bottom:100%;
    position:relative;
    overflow:hidden;
  }

  .media__imgCentered {
    top:0;
    bottom:0;
    left:0;
    right:0;
    position: absolute;
    display: flex;
    justify-content: center;
    align-items: center;
    background: $color__lighter no-repeat center center;
    background-size: 100% auto;
    transition: background-image 350ms cubic-bezier(0.795, 0.125, 0.280, 0.990), background-size 0ms;

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

    &.media__img--landscape {
      background-size: 100% auto;
    }

    &.media__img--portrait {
      background-size: auto 100%;
    }
  }

  .media__edit {
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    display: block;
    opacity:0;
    background-color: rgba(0, 0, 0, 0.2);
    cursor: pointer;
    transition: opacity 0.3s ease;

    .media__edit--button {
      display: block;
      position: absolute;
      right: 10px;
      bottom: 10px;
      height: 26px;
      width: 26px;
      line-height: 26px;
      text-align: center;
      border-radius: 50%;
      background: $color__background;
      color: $color__icons;

      .icon {
        color: $color__icons;
        transition: color .25s linear;
      }
    }

    .media__imgFrame:hover & {
      opacity:1;
    }
  }

  .media__info {
    display: flex;
    flex-direction: row;
    flex-wrap: nowrap;
    align-items: flex-start;
    align-content: flex-start;
  }

  .media__metadatas {
    padding: 5px 15px;
    flex-grow: 1;
    color: $color__text--light;
    overflow: hidden;

    li {
      overflow:hidden;
    }

    a {
      color:$color__link;
    }
  }

  .media__name {
    strong {
      font-weight: normal;
      color: $color__text;
      overflow:hidden;
      text-overflow:ellipsis;
      display:block;
      margin-bottom:5px;
      // white-space: nowrap;
    }

    &:hover {
      cursor: pointer;

      strong {
        color: $color__link;
      }
    }
  }

  .media__metadatas--options {
    display: none;
    margin-top: 35px;
  }

  .media__metadatas--options.s--active {
    display: block;
  }

  .media__actions {
    min-width:45px * 3;

    @media screen and (max-width: 1140px) {
      display: none !important;
    }

    .s--in-editor &{
      display: none!important;
    }
  }

  .media__actions-dropDown {
    @media screen and (min-width: 1139px) {
      display: none;
    }

    .s--in-editor & {
      display: block!important;
    }
  }

  .media__actions-dropDown /deep/ .dropdown__content {
    margin-top: 10px;
  }

  .media.media--hoverable {
    .media__actions {
      opacity: 0;
      transition: opacity 250ms ease;
    }

    :hover .media__actions {
      opacity: 1;
    }
  }

  /* Modal with cropper */
  .modal--cropper .cropper__button {
    width:100%;
    display:block;
    margin-top:20px;
    margin-bottom:20px;

    @include breakpoint('small+') {
      position: absolute;
      bottom: 0;
      left: 0;
      width:auto;
      margin-top:20px;
      margin-bottom:20px;
    }
  }
</style>
