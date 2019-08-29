<template>
  <div class="slideshow">
    <draggable class="slideshow__content" v-model="slides" :options="dragOptions" v-if="slides.length">
      <transition-group name="draggable_list" tag='div'>
        <div class="slide" v-for="(slide, index) in slides" :key="index">
            <div class="slide__handle">
              <div class="slide__handle--drag"></div>
            </div>
            <a17-mediafield class="slide__content" :name="`${name}_${slide.id}`" :index="index" :mediaContext="name" :cropContext="cropContext" :hover="hoverable" :isSlide="true" :withAddInfo="withAddInfo" :withCaption="withCaption" :withVideoUrl="withVideoUrl" :extraMetadatas="extraMetadatas"></a17-mediafield>
        </div>
      </transition-group>
    </draggable>
    <div class="slideshow__trigger" v-if="remainingSlides > 0">
      <a17-button type="button" variant="ghost" @click="openMediaLibrary(remainingSlides)">{{ addLabel }}</a17-button>
      <span class="slideshow__note f--small"><slot></slot></span>
    </div>
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import { MEDIA_LIBRARY } from '@/store/mutations'

  import draggableMixin from '@/mixins/draggable'
  import mediaLibrayMixin from '@/mixins/mediaLibrary/mediaLibrary.js'

  import draggable from 'vuedraggable'

  export default {
    name: 'A17Slideshow',
    components: {
      draggable
    },
    mixins: [draggableMixin, mediaLibrayMixin],
    props: {
      name: {
        type: String,
        required: true
      },
      cropContext: {
        type: String,
        default: ''
      },
      itemLabel: {
        type: String,
        default: 'image'
      },
      max: {
        type: Number,
        default: 10
      },
      withAddInfo: {
        type: Boolean,
        default: true
      },
      withCaption: {
        type: Boolean,
        default: true
      },
      withVideoUrl: {
        type: Boolean,
        default: true
      },
      extraMetadatas: {
        type: Array,
        default () {
          return []
        }
      }
    },
    data: function () {
      return {
        handle: '.slide__handle', // Drag handle override
        hoverable: true
      }
    },
    computed: {
      remainingSlides: function () {
        return Math.max(0, this.max - this.slides.length)
      },
      addLabel: function () {
        const itemNames = this.itemLabel + 's'
        return 'Attach ' + itemNames
      },
      slides: {
        get () {
          if (this.selectedMedias.hasOwnProperty(this.name)) {
            return this.selectedMedias[this.name] || []
          } else {
            return []
          }
        },
        set (value) {
          this.$store.commit(MEDIA_LIBRARY.REORDER_MEDIAS, {
            name: this.name,
            medias: value
          })
        }
      },
      ...mapState({
        selectedMedias: state => state.mediaLibrary.selected
      })
    },
    methods: {
      deleteSlideshow: function () {
        // destroy all the medias of the slideshow
        this.$store.commit(MEDIA_LIBRARY.DESTROY_MEDIAS, this.name)
      }
    },
    beforeDestroy: function () {
      this.deleteSlideshow()
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .slideshow {
    // width: 100%;
    display: block;
    border-radius: 2px;
    border: 1px solid $color__border;
    /*overflow-x: hidden;*/
    background:$color__background;
  }

  .slideshow__trigger {
    padding:10px;
    position:relative;
    border-top: 1px solid $color__border--light;

    &:first-child {
      border-top:0 none
    }
  }

  .slideshow__note {
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

    // .s--in-editor & {
    //   @include breakpoint('small+') {
    //     display: none;
    //   }
    // }
  }

  .slide {
    display: flex;
    flex-direction: row;
    border-bottom: 1px solid $color__border--light;
    background-color: $color__background;

    &:last-child {
      border-bottom: 0 none;
    }
  }

  .slide__handle {
    cursor: move;
    display: flex;
    justify-content: center;
    align-items: center;
    width: 12px;
    min-width:12px;
    background-color: $color__drag_bg;
    transition: background 250ms ease;

    &:hover {
      background-color: $color__drag_bg--hover;
    }
  }

  .slide__handle:hover .slide__handle--drag:before {
    background: dragGrid__bg($color__drag_bg--hover);
  }

  .slide__handle--drag {
    position: relative;
    width: 10px;
    height: 52px;
    transition: background 250ms ease;
    @include dragGrid($color__drag, $color__drag_bg);
  }

  .slide__content {
    flex-grow: 1;
    max-width:calc(100% - 12px);
  }

</style>
