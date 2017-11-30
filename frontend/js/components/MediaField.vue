<template>
  <div class="media" :class="{ 'media--hoverable' : hover, 'media--slide' : isSlide }">
    <div class="media__field">
      <div class="media__info" v-if="hasMedia">
        <div class="media__img">
          <img :src="currentMedia.src"/>
          <input type="hidden" :name="name" :value="currentMedia.id"/>
        </div>

        <ul class="media__metadatas">
          <li><strong>{{ currentMedia.name }}</strong></li>
          <li class="f--small" v-if="currentMedia.size">File size : {{ currentMedia.size }}</li>
          <li class="f--small">Dimensions : {{ currentMedia.width }} x {{ currentMedia.height }}</li>
          <li class="f--small media__metadatas--add">
            <a href="#" @click.prevent="metadatasInfos"> {{ metadatas.text }}</a></li>
        </ul>

        <!--Actions-->
        <a17-buttonbar class="media__actions">
          <a :href="currentMedia.original" download><span v-svg symbol="download"></span></a>
          <button type="button" @click="openCropMedia" v-if="hasCrop"><span v-svg symbol="crop"></span></button>
          <button type="button" @click="deleteMedia"><span v-svg symbol="trash"></span></button>
        </a17-buttonbar>

        <div class="media__actions-dropDown">
          <a17-dropdown ref="dropDown" position="right">
            <a17-button size="smallIcon" variant="icon" @click="$refs.dropDown.toggle()">
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
        <a17-inputframe label="Alt Text">
          <div class="form__field">
            <input type="text" :name="`${name}_text`" :placeholder="altText" v-model="altText">
          </div>
        </a17-inputframe>

        <a17-inputframe label="Caption">
          <div class="form__field">
            <input type="text" :name="`${name}_caption`" :placeholder="caption" v-model="caption">
          </div>
        </a17-inputframe>

        <a17-inputframe label="Video URL (optional)">
          <div class="form__field">
            <input type="text" :name="`${name}_video`" placeholder="Youtube or Vimeo URL" v-model="video">
          </div>
        </a17-inputframe>
      </div>
    </div>

    <!-- Crop modal -->
    <a17-modal class="modal--cropper" :ref="cropModalName" title="Edit image crop" mode="medium" v-if="hasMedia">
      <a17-cropper :media="currentMedia" v-on:crop-end="cropMedia" :aspectRatio="16 / 9" :context="cropContext">
        <a17-button class="cropper__button" variant="action" @click="$refs[cropModalName].close()">Update</a17-button>
      </a17-cropper>
    </a17-modal>
  </div>
</template>

<script>
  import { mapState } from 'vuex'

  import a17Cropper from '@/components/Cropper.vue'
  import mediaLibrayMixin from '@/mixins/mediaLibrary.js'

  export default {
    name: 'A17Mediafield',
    components: {
      'a17-cropper': a17Cropper
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
      }
    },
    data: function () {
      return {
        metadatas: {
          text: 'Add info',
          active: false
        }
      }
    },
    computed: {
      mediaKey: function () {
        return this.mediaContext.length > 0 ? this.mediaContext : this.name
      },
      currentMedia: function () {
        if (this.selectedMedias.hasOwnProperty(this.mediaKey)) {
          return this.selectedMedias[this.mediaKey][this.index] || {}
        } else {
          return {}
        }
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
      altText: {
        get: function () {
          if (this.currentMedia.hasOwnProperty('metadatas')) {
            let altText = this.currentMedia.metadatas.custom.altText
            return altText !== null && altText > 0 ? altText : this.currentMedia.metadatas.default.altText
          } else {
            return ''
          }
        },
        set: function (val) {
          this.$store.commit('setMediaMetadatas', {
            media: {
              context: this.mediaKey,
              index: this.index
            },
            values: {
              altText: val
            }
          })
        }
      },
      caption: {
        get: function () {
          if (this.currentMedia.hasOwnProperty('metadatas')) {
            let caption = this.currentMedia.metadatas.custom.caption
            return caption !== null ? caption : this.currentMedia.metadatas.default.caption
          } else {
            return ''
          }
        },
        set: function (val) {
          this.$store.commit('setMediaMetadatas', {
            media: {
              context: this.mediaKey,
              index: this.index
            },
            values: {
              caption: val
            }
          })
        }
      },
      video: {
        get: function () {
          if (this.currentMedia.hasOwnProperty('metadatas')) {
            let video = this.currentMedia.metadatas.custom.video
            return video !== null ? video : this.currentMedia.metadatas.default.video
          } else {
            return ''
          }
        },
        set: function (val) {
          this.$store.commit('setMediaMetadatas', {
            media: {
              context: this.mediaKey,
              index: this.index
            },
            values: {
              video: val
            }
          })
        }
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
            defaultCrops[cropVariant].width = this.currentMedia.width / 2
            defaultCrops[cropVariant].height = this.currentMedia.height / 2
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
        this.$store.commit('destroyMediasInSelected', {name: this.mediaKey, index: this.index})
      },
      // metadatas
      metadatasInfos: function () {
        let self = this
        self.metadatas.active = !self.metadatas.active
        self.metadatas.text = self.metadatas.active ? 'Close info' : 'Add info'
      }
    },
    beforeDestroy: function () {
      this.deleteMedia()
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
    border: 1px solid $color__fborder;
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
    max-width: 270px;
    user-select: none;
    position:relative;

    display: flex;
    justify-content: center;
    align-items: center;

    &:before {
      content: "";
      position: absolute;
      display:block;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      border:1px solid rgba(0,0,0,0.1);
    }

    img {
      display:block;
      max-width:100%;
      height:auto;
      max-height:100%;
    }
  }

  // Image centered in a square option :

  // <div class="media__img">
  //   <div class="media__imgFrame">
  //     <div class="media__imgCentered">
  //       <img :src="currentMedia.src"/>
  //     </div>
  //   </div>
  // </div>

  // .media__imgFrame {
  //   width:100%;
  //   padding-bottom:100%;
  //   position:relative;
  //   overflow:hidden;
  // }

  // .media__imgCentered {
  //   top:0;
  //   bottom:0;
  //   left:0;
  //   right:0;
  //   position: absolute;
  //   display: flex;
  //   justify-content: center;
  //   align-items: center;

  //   &:before {
  //     content: "";
  //     position: absolute;
  //     display:block;
  //     top: 0;
  //     left: 0;
  //     right: 0;
  //     bottom: 0;
  //     border:1px solid rgba(0,0,0,0.1);
  //   }
  // }

  .media--slide .media__img {
    max-width: 180px;
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

    strong {
      font-weight: normal;
      color: $color__text;
    }
  }

  .media__metadatas--add {

    a {
      color: $color__link;
      text-decoration: none;

      &:hover {
        text-decoration: underline;
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
    @media screen and (max-width: 1140px) {
      display: none;
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
