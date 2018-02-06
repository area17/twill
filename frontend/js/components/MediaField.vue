<template>
  <div class="media" :class="{ 'media--hoverable' : hover, 'media--slide' : isSlide }">
    <div class="media__field">
      <div class="media__info" v-if="hasMedia">
        <div class="media__img">
          <div class="media__imgFrame">
            <div class="media__imgCentered">
              <img :src="currentMedia.src"/>
            </div>
          </div>
        </div>

        <ul class="media__metadatas">
          <li class="media__name"><strong :title="currentMedia.name">{{ currentMedia.name }}</strong></li>
          <li class="f--small" v-if="currentMedia.size">File size: {{ currentMedia.size | uppercase }}</li>
          <li class="f--small" v-if="currentMedia.width + currentMedia.height">Dimensions: {{ currentMedia.width }}&nbsp;&times;&nbsp;{{ currentMedia.height }}</li>
          <li class="f--small" v-if="cropInfos.length" @click="openCropMedia">
            <span class="f--small f--note f--underlined--o f--underlined--link">
              Cropped : <span v-for="(cropInfo, index) in cropInfos" :key="cropInfo.name">{{ cropInfo.name }}<span v-if="index !== cropInfos.length - 1">,&nbsp;</span></span>
            </span>
          </li>
          <li class="f--small">
            <a href="#" @click.prevent="metadatasInfos" v-if="withAddInfo" class="f--link-underlined--o">{{ metadatas.text }}</a>
          </li>
        </ul>

        <!--Actions-->
        <a17-buttonbar class="media__actions">
          <a :href="currentMedia.original" download><span v-svg symbol="download"></span></a>
          <button type="button" @click="openCropMedia" v-if="hasCrop"><span v-svg symbol="crop"></span></button>
          <button type="button" @click="deleteMedia"><span v-svg symbol="trash"></span></button>
        </a17-buttonbar>

        <div class="media__actions-dropDown">
          <a17-dropdown ref="dropDown" position="right">
            <a17-button size="icon" variant="icon" @click="$refs.dropDown.toggle()">
              <span v-svg symbol="more-dots"></span></a17-button>
            <div slot="dropdown__content">
              <a :href="currentMedia.original" download><span v-svg symbol="download"></span> Download</a>
              <button type="button" @click="openCropMedia" v-if="hasCrop"><span v-svg symbol="crop"></span> Crop
              </button>
              <button type="button" @click="deleteMedia"><span v-svg symbol="trash"></span> Delete</button>
            </div>
          </a17-dropdown>
        </div>
      </div>

      <!--Add media button-->
      <a17-button variant="ghost" @click="openMediaLibrary" :disabled="disabled" v-if="!hasMedia">{{ btnLabel }}</a17-button>
      <p class="media__note f--small" v-if="!!this.$slots.default">
        <slot></slot>
      </p>

      <!-- Metadatas options -->
      <div class="media__metadatas--options" :class="{ 's--active' : metadatas.active }" v-if="hasMedia">
        <a17-mediametadata :name='name' label="Alt Text" id="altText" :media="currentMedia" @change="updateMetadata"></a17-mediametadata>
        <a17-mediametadata :name='name' label="Caption" id="caption" :media="currentMedia" @change="updateMetadata"></a17-mediametadata>
        <a17-mediametadata v-if="withVideoUrl" :name='name' label="Video URL (optional)" id="video" :media="currentMedia" @change="updateMetadata"></a17-mediametadata>
      </div>
    </div>

    <!-- Crop modal -->
    <a17-modal class="modal--cropper" :ref="cropModalName" :forceClose="true" title="Edit image crop" mode="medium" v-if="hasMedia">
      <a17-cropper :media="currentMedia" v-on:crop-end="cropMedia" :aspectRatio="16 / 9" :context="cropContext">
        <a17-button class="cropper__button" variant="action" @click="$refs[cropModalName].close()">Update</a17-button>
      </a17-cropper>
    </a17-modal>
  </div>
</template>

<script>
  import { mapState } from 'vuex'

  import a17Cropper from '@/components/Cropper.vue'
  import a17MediaMetadata from '@/components/MediaMetadata.vue'
  import mediaLibrayMixin from '@/mixins/mediaLibrary.js'
  import a17VueFilters from '@/utils/filters.js'

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
      }
    },
    data: function () {
      return {
        metadatas: {
          text: 'Edit info',
          textOpen: 'Edit info',
          textClose: 'Close info',
          isDestroyed: false,
          active: false
        }
      }
    },
    filters: a17VueFilters,
    computed: {
      mediaKey: function () {
        return this.mediaContext.length > 0 ? this.mediaContext : this.name
      },
      currentMedia: function () {
        if (this.selectedMedias.hasOwnProperty(this.mediaKey)) {
          // reset is destroyed
          if (this.selectedMedias[this.mediaKey][this.index]) this.isDestroyed = false
          return this.selectedMedias[this.mediaKey][this.index] || {}
        } else {
          return {}
        }
      },
      cropInfos: function () {
        const cropInfos = []

        if (this.currentMedia.crops) {
          for (let variant in this.currentMedia.crops) {
            cropInfos.push({
              name: this.currentMedia.crops[variant].name,
              width: this.currentMedia.crops[variant].width,
              height: this.currentMedia.crops[variant].height
            })
          }
        }

        return cropInfos
      },
      hasCrop: function () {
        return this.crop !== ''
      },
      hasMedia: function () {
        return Object.keys(this.currentMedia).length
      },
      cropModalName: function () {
        return `${name}Modal`
      },
      ...mapState({
        selectedMedias: state => state.mediaLibrary.selected,
        allCrops: state => state.mediaLibrary.crops
      })
    },
    methods: {
      // crop
      setDefaultCrops: function () {
        let self = this

        if (!this.hasCrop) return

        let defaultCrops = {}

        if (self.allCrops.hasOwnProperty(self.cropContext)) {
          for (let cropVariant in self.allCrops[self.cropContext]) {
            defaultCrops[cropVariant] = {}
            defaultCrops[cropVariant].name = self.allCrops[self.cropContext][cropVariant][0].name || cropVariant
            defaultCrops[cropVariant].x = 0
            defaultCrops[cropVariant].y = 0
            defaultCrops[cropVariant].width = this.currentMedia.width
            defaultCrops[cropVariant].height = this.currentMedia.height
          }
        }

        this.cropMedia({values: defaultCrops})
      },
      cropMedia: function (crop) {
        crop.key = this.mediaKey
        crop.index = this.index
        this.$store.commit('setMediaCrop', crop)
      },
      openCropMedia: function () {
        if (!this.currentMedia.crops) this.setDefaultCrops()
        this.$refs[this.cropModalName].open()
      },
      // media
      deleteMedia: function () {
        this.isDestroyed = true
        this.$store.commit('destroyMediasInSelected', {name: this.mediaKey, index: this.index})
      },
      // metadatas
      updateMetadata: function (newValue) {
        this.$store.commit('setMediaMetadatas', {
          media: {
            context: this.mediaKey,
            index: this.index
          },
          value: newValue
        })
      },
      metadatasInfos: function () {
        let self = this
        self.metadatas.active = !self.metadatas.active
        self.metadatas.text = self.metadatas.active ? self.metadatas.textClose : self.metadatas.textOpen
      }
    },
    beforeDestroy: function () {
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
      height:auto;
      max-height:100%;
    }
  }

  .media--slide .media__img {
    max-width: 120px;
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
    background:$color__ultralight;

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
  }

  .media__actions-dropDown {
    @media screen and (min-width: 1139px) {
      display: none;
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
    position: absolute;
    bottom: 20px;
    left: 0;
  }
</style>
