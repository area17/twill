<template>
  <div class="slideshow">
    <draggable class="slideshow__content" v-model="slides" :options="dragOptions">
      <transition-group name="draggable_list" tag='div'>
        <div class="slide" v-for="(slide, index) in slides" :key="slide.id">
            <div class="slide__handle">
              <div class="slide__handle--drag"></div>
            </div>
            <a17-mediafield class="slide__content" :name="`${name}_${slide.id}`" :index="index" :mediaContext="name" :cropContext="cropContext" :hover="hoverable" :isSlide="true"></a17-mediafield>
        </div>
      </transition-group>
    </draggable>
    <div class="slideshow__trigger">
      <a17-button type="button" variant="ghost" @click="openMediaLibrary(remainingSlides)" :disabled="!remainingSlides">{{ addLabel }}</a17-button>
      <span class="slideshow__note f--small"><slot></slot></span>
    </div>
  </div>
</template>

<script>
  import { mapState } from 'vuex'

  import draggableMixin from '@/mixins/draggable'
  import mediaLibrayMixin from '@/mixins/mediaLibrary.js'

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
        return this.max - this.slides.length
      },
      addLabel: function () {
        const itemNames = this.remainingSlides > 1 ? 'up to ' + this.remainingSlides + ' ' + this.itemLabel + 's' : this.remainingSlides + ' ' + this.itemLabel
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
          this.$store.commit('reorderSelectedMedias', {
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
        this.$store.commit('destroySelectedMedias', this.name)
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
    overflow-x: hidden;
  }

  .slideshow__trigger {
    padding:10px;
    position:relative;
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
  }

  .slide {
    display: flex;
    flex-direction: row;
    border-bottom: 1px solid $color__border--light;
    background-color: $color__background;
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
    background: repeating-linear-gradient(180deg, $color__drag_bg--hover 0, $color__drag_bg--hover 2px, transparent 2px, transparent 4px);
  }

  .slide__handle--drag {
    position: relative;
    width: 6px;
    height: 52px;
    background: repeating-linear-gradient(90deg, $color__drag 0, $color__drag 2px, transparent 2px, transparent 4px);
    transition: background 250ms ease;

    &:before {
      content: '';
      position: absolute;
      width: 100%;
      height: 100%;
      background: repeating-linear-gradient(180deg, $color__drag_bg 0, $color__drag_bg 2px, transparent 2px, transparent 4px);
    }
  }

  .slide__content {
    flex-grow: 1;
  }

</style>
