<template>
  <div class="media" :class="{ 'media--hoverable' : hover, 'media--slide' : isSlide }">
    <div class="media__field">
      <div class="media__info" v-if="hasMedia">
        <div class="media__img">
          <div class="media__imgFrame">
            <div class="media__imgCentered" :style="cropThumbnailStyle">
              <img v-if="cropSrc && showImg" :src="cropSrc" ref="mediaImg" :class="cropThumbnailClass"/>
            </div>
            <div class="media__edit" @click="openMediaLibrary(1, mediaKey, index)" v-if="!disabled">
              <span class="media__edit--button"><span v-svg symbol="edit"></span></span>
            </div>
          </div>
        </div>

        <ul class="media__metadatas" v-if="!disabled">
          <li class="media__name" @click="openMediaLibrary(1, mediaKey, index)"><strong :title="media.name">{{
            media.name }}</strong></li>
          <li class="f--small" v-if="media.size">File size: {{ media.size | uppercase }}</li>
          <li class="f--small" v-if="media.width + media.height">{{ $trans('fields.medias.original-dimensions') }}: {{ media.width }}&nbsp;&times;&nbsp;{{
            media.height }}
          </li>
          <li class="f--small media__crop-link" v-if="cropInfos && activeCrop" @click="openCropMedia">
              <p class="f--small f--note hide--xsmall"
                 v-for="(cropInfo, index) in cropInfos"
                 :key="index">
                <span v-html="cropInfo"></span>
              </p>
          </li>
          <li class="f--small">
            <a href="#" @click.prevent="metadatasInfos" v-if="withAddInfo" class="f--link-underlined--o">{{ metadatas.text }}</a>
          </li>
        </ul>

        <!--Actions-->
        <a17-buttonbar class="media__actions" v-if="!disabled">
          <a :href="media.original" download><span v-svg symbol="download"></span></a>
          <button type="button" @click="openCropMedia" v-if="activeCrop"><span v-svg symbol="crop"></span></button>
          <button type="button" @click="deleteMediaClick"><span v-svg symbol="trash"></span></button>
        </a17-buttonbar>

        <div class="media__actions-dropDown">
          <a17-dropdown ref="dropDown" position="right">
            <a17-button size="icon" variant="icon" @click="$refs.dropDown.toggle()">
              <span v-svg symbol="more-dots"></span></a17-button>
            <div slot="dropdown__content">
              <a :href="media.original" download><span v-svg symbol="download"></span>{{ $trans('fields.medias.download') }}</a>
              <button type="button" @click="openCropMedia" v-if="activeCrop"><span v-svg symbol="crop"></span>{{ $trans('fields.medias.crop') }}
              </button>
              <button type="button" @click="deleteMediaClick"><span v-svg symbol="trash"></span>{{ $trans('fields.medias.delete') }}</button>
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
      <div class="media__metadatas--options" :class="{ 's--active' : metadatas.active }" v-if="hasMedia && withAddInfo">
        <a17-mediametadata :name='metadataName' :label="$trans('fields.medias.alt-text', 'Alt Text')" id="altText" :media="media" :maxlength="altTextMaxLength" @change="updateMetadata"/>

        <a17-mediametadata v-if="withCaption" :wysiwyg="useWysiwyg" :wysiwyg-options="wysiwygOptions" type='text' :name='metadataName' :label="$trans('fields.medias.caption', 'Caption')" id="caption" :media="media" :maxlength="captionMaxLength" @change="updateMetadata"/>

        <a17-mediametadata v-if="withVideoUrl" :name='metadataName' :label="$trans('fields.medias.video-url', 'Video URL (optional)')" id="video" :media="media" @change="updateMetadata"/>

        <template v-for="field in extraMetadatas">
          <a17-mediametadata v-if="extraMetadatas.length > 0"
                             :key="field.name"
                             :type="field.type"
                             :name='metadataName'
                             :wysiwyg='field.wysiwyg || false'
                             :wysiwyg-options='field.wysiwygOptions || wysiwygOptions'
                             :label="field.label"
                             :id="field.name"
                             :media="media"
                             :maxlength="field.maxlength || 0"
                             @change="updateMetadata"/>
        </template>
      </div>
    </div>

    <!-- Crop modal -->
    <a17-modal class="modal--cropper" :ref="cropModalName" :forceClose="true" :title="$trans('fields.medias.crop-edit')" mode="medium" v-if="hasMedia && activeCrop">
      <a17-cropper :media="media" v-on:crop-end="cropMedia" :aspectRatio="16 / 9" :context="cropContext" :key="cropperKey">
        <a17-button class="cropper__button" variant="action" @click="$refs[cropModalName].close()">{{ $trans('fields.medias.crop-save') }}</a17-button>
      </a17-cropper>
    </a17-modal>
    <input :name="inputName" type="hidden" :value="JSON.stringify(media)" />
  </div>
</template>

<script>
  import { mapState } from 'vuex'

  import { MEDIA_LIBRARY } from '@/store/mutations'

  import a17Cropper from '@/components/Cropper.vue'
  import a17MediaMetadata from '@/components/MediaMetadata.vue'
  import mediaLibrayMixin from '@/mixins/mediaLibrary/mediaLibrary.js'
  import mediaFieldMixin from '@/mixins/mediaField.js'

  import a17VueFilters from '@/utils/filters.js'
  import { cropConversion } from '@/utils/cropper'
  import smartCrop from 'smartcrop'

  const IS_SAFARI = navigator.userAgent.indexOf('Safari') !== -1 && navigator.userAgent.indexOf('Chrome') === -1

  export default {
    name: 'A17Mediafield',
    components: {
      'a17-cropper': a17Cropper,
      'a17-mediametadata': a17MediaMetadata
    },
    mixins: [mediaLibrayMixin, mediaFieldMixin],
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
        default () {
          return this.$trans('fields.medias.btn-label', 'Attach image')
        }
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
      activeCrop: {
        type: Boolean,
        default: true
      },
      widthMin: {
        type: Number,
        default: 0
      },
      heightMin: {
        type: Number,
        default: 0
      }
    },
    data: function () {
      return {
        canvas: null,
        img: null,
        ctx: null,
        imgLoaded: false,
        cropSrc: '',
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
        hasMediaChanged: false,
        metadatas: {
          text: this.$trans('fields.medias.edit-info'),
          textOpen: this.$trans('fields.medias.edit-info'),
          textClose: this.$trans('fields.medias.edit-close'),
          active: false
        }
      }
    },
    filters: a17VueFilters,
    computed: {
      ...mapState({
        useWysiwyg: state => state.mediaLibrary.config.useWysiwyg,
        wysiwygOptions: state => state.mediaLibrary.config.wysiwygOptions
      }),
      cropThumbnailStyle: function () {
        if (this.showImg) return {}
        if (!this.hasMedia) return {}
        if (!this.media.crops) return {}
        if (this.cropSrc.length === 0) return {}

        return {
          backgroundImage: `url(${this.cropSrc})`
        }
      },
      cropThumbnailClass: function () {
        if (!this.hasMedia) return {}
        if (!this.media.crops) return {}
        const crop = this.media.crops[Object.keys(this.media.crops)[0]]
        return {
          'media__img--landscape': crop.width / crop.height >= 1,
          'media__img--portrait': crop.width / crop.height < 1
        }
      },
      mediaKey: function () {
        return this.mediaContext.length > 0 ? this.mediaContext : this.name
      },
      inputName: function () {
        let fieldName = this.name
        if (this.name.indexOf('[')) {
          fieldName = this.name.replace(']', '').replace('[', '][')
        }
        return 'medias[' + fieldName + '][' + this.index + ']'
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
        const cropInfos = []
        if (this.media.crops) {
          for (const variant in this.media.crops) {
            if (this.media.crops[variant].width + this.media.crops[variant].height) { // crop is not 0x0
              let cropInfo = ''
              cropInfo += this.media.crops[variant].name + ' ' + this.$trans('fields.medias.crop-list') + ': '
              cropInfo += this.media.crops[variant].width + '&nbsp;&times;&nbsp;' + this.media.crops[variant].height
              cropInfos.push(cropInfo)
            }
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
        this.hasMediaChanged = val !== oldVal

        if (this.selectedMedias.hasOwnProperty(this.mediaKey)) {
          // reset isDestroyed status because we changed the media
          if (this.selectedMedias[this.mediaKey][this.index]) this.isDestroyed = false
        }
      }
    },
    methods: {
      // crop
      canvasCrop () {
        const data = this.media.crops[Object.keys(this.media.crops)[0]]
        if (!data) return

        // in case of a 0x0 crop : let's display the full image in the preview
        if (data.width + data.height === 0) {
          this.showDefaultThumbnail()
          return
        }

        // default src
        let src = this.media.thumbnail

        this.$nextTick(() => {
          try {
            const crop = cropConversion(data, this.naturalDim, this.originalDim)
            const cropWidth = crop.width
            const cropHeight = crop.height
            this.canvas.width = cropWidth
            this.canvas.height = cropHeight
            this.ctx.drawImage(this.img, crop.x, crop.y, cropWidth, cropHeight, 0, 0, cropWidth, cropHeight)
            src = this.canvas.toDataURL('image/png')

            // show data url in the background
            if (this.cropSrc !== src) {
              this.showImg = false
              this.cropSrc = src
            }
          } catch (error) {
            console.error(error)

            // fallback on displaying the thumbnail
            if (this.cropSrc !== src) {
              this.showImg = true
              this.cropSrc = src
            }
          }
        })
      },
      setDefaultCrops: function () {
        const defaultCrops = {}
        const smarcrops = []

        if (this.allCrops.hasOwnProperty(this.cropContext)) {
          for (const cropVariant in this.allCrops[this.cropContext]) {
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

            smarcrops.push(smartCrop.crop(this.img, { width: crop.width, height: crop.height, minScale: 1.0 }))

            const x = Math.floor(center.x - cropWidth / 2)
            const y = Math.floor(center.y - cropHeight / 2)

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
            this.cropMedia({ values: defaultCrops })
          }, (error) => {
            console.error(error)
            this.cropMedia({ values: defaultCrops })
          })
        } else {
          this.cropMedia({ values: defaultCrops })
        }
      },
      cropMedia: function (crop) {
        crop.key = this.mediaKey
        crop.index = this.index
        this.$store.commit(MEDIA_LIBRARY.SET_MEDIA_CROP, crop)
        if (this.img) this.canvasCrop()
      },
      setNaturalDimensions: function () {
        if (this.img) {
          this.naturalDim.width = this.img.naturalWidth
          this.naturalDim.height = this.img.naturalHeight
        }
      },
      setOriginalDimensions: function () {
        if (this.media) {
          this.originalDim.width = this.media.width
          this.originalDim.height = this.media.height
        }
      },
      init: function () {
        this.showImg = false

        const imgLoaded = () => {
          this.setNaturalDimensions()
          this.setOriginalDimensions()

          if (!this.mediaHasCrop) {
            this.setDefaultCrops()
          } else {
            this.canvasCrop()
          }
        }

        if (this.hasMedia) {
          this.cropSrc = this.media.thumbnail

          this.initImg().then(() => {
            imgLoaded()
          }, (error) => {
            console.error(error)
            this.showDefaultThumbnail()

            // lets try to load to image tag now
            this.$nextTick(() => {
              // the image tag
              const imgTag = this.$refs.mediaImg
              if (imgTag) {
                imgTag.addEventListener('load', () => {
                  this.img = imgTag
                  imgLoaded()
                }, {
                  once: true,
                  passive: true,
                  capture: true
                })

                imgTag.addEventListener('error', (e) => {
                  console.error(e)
                  this.showDefaultThumbnail()
                })
              } else {
                this.showImg = false
                this.cropSrc = this.media.thumbnail
              }
            })
          })
          this.hasMediaChanged = false
        }
      },
      initImg: function () {
        return new Promise((resolve, reject) => {
          this.img = new Image()
          if (!IS_SAFARI) {
            this.img.crossOrigin = 'Anonymous'
          }
          this.canvas = document.createElement('canvas')
          this.ctx = this.canvas.getContext('2d')

          this.img.addEventListener('load', () => {
            resolve()
          }, {
            once: true,
            passive: true,
            capture: true
          })

          // in case of CORS issue or anything else
          this.img.addEventListener('error', (e) => {
            reject(e)
          })

          // try to load the media thumbnail
          let append = '?'
          if (this.media.thumbnail.indexOf('?') > -1) {
            append = '&'
          }
          this.img.src = this.media.thumbnail + append + 'no-cache'
        })
      },
      showDefaultThumbnail: function () {
        this.showImg = true
        if (this.hasMedia) this.cropSrc = this.media.thumbnail
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
      },
      destroyValue: function () {
        if (this.isSlide) return // for Slideshows : the medias are deleted when the slideshow component is destroyed (so no need to do it here)
        if (!this.isDestroyed) this.deleteMedia()
      }
    },
    beforeMount: function () {
      this.init()
    },
    beforeUpdate: function () {
      if (this.hasMediaChanged) {
        this.init()
      }
    }
  }
</script>

<style lang="scss" scoped>

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
      margin:auto;

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
    text-decoration: none;
    cursor: pointer;

    p:first-letter {
      text-transform: capitalize;
    }

    &:hover .f--small span {
      @include bordered($color__text, false);
    }

    @include breakpoint('medium-') {
      flex-direction: column;
    }
  }

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
    background-color: $color__lighter;
    background-size: contain;
    background-repeat:no-repeat;
    background-position:center center;
    transition: background-image 350ms cubic-bezier(0.795, 0.125, 0.280, 0.990), background-size 0ms 350ms;

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

<style lang="scss">
  .media .media__actions-dropDown .dropdown__content {
    margin-top: 10px;
  }
</style>
