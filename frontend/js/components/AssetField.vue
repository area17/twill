<template>
  <a17-inputframe :error="error" :label="label" :locale="locale" @localize="updateLocale" :size="size" :name="name" :note="fieldNote">
    <div class="assetField">

      <div class="assetField__trigger" v-if="buttonOnTop && remainingItems">
        <a17-button type="button" variant="ghost" @click="openMediaLibrary(remainingItems)">{{ addLabel }}</a17-button>
        <span class="assetField__note f--small">{{ note }}</span>
      </div>
      <draggable class="assetField__content" v-model="items" v-bind="dragOptions" v-if="items.length">
        <transition-group name="draggable_list" tag='div'>
          <div class="asset" v-for="(item, index) in items" :key="`${item.id}_${index}`">
            <div class="asset__handle" v-if="!disabled">
              <div class="asset__handle--drag"></div>
            </div>
            <a17-mediafield class="asset__content"
                            :name="`${name}_${item.id}`"
                            :index="index"
                            :mediaContext="name"
                            :cropContext="cropContext"
                            :hover="hoverable"
                            :isSlide="true"
                            :withAddInfo="withAddInfo"
                            :withCaption="withCaption"
                            :withVideoUrl="withVideoUrl"
                            :altTextMaxLength="altTextMaxLength"
                            :captionMaxLength="captionMaxLength"
                            :extraMetadatas="extraMetadatas"
                            :disabled="disabled">
            </a17-mediafield>
          </div>
        </transition-group>
      </draggable>
      <div class="assetField__trigger" v-if="!buttonOnTop && remainingItems">
        <a17-button type="button" variant="ghost" @click="openMediaLibrary(remainingItems)">{{ addLabel }}</a17-button>
        <span class="assetField__note f--small">{{ note }}</span>
      </div>
    </div>
  </a17-inputframe>
</template>

<script>
  import draggable from 'vuedraggable'
  import inputframeMixin from '@/mixins/inputFrame'
  import draggableMixin from "@/mixins/draggable";
  import mediaLibraryMixin from "@/mixins/mediaLibrary/mediaLibrary";
  import mediaFieldMixin from '@/mixins/mediaField.js'
  import localeMixin from "@/mixins/locale";
  import {MEDIA_LIBRARY} from "@/store/mutations";
  import {mapState} from "vuex";


  export default {
    name: "A17AssetField",
    components: {
      draggable
    },
    mixins: [draggableMixin, mediaLibraryMixin, localeMixin, inputframeMixin, mediaFieldMixin],
    props: {
      fieldNote: {
        type: String,
        default: ''
      },
      name: {
        type: String,
        required: true
      },
      itemLabel: {
        type: String,
        default: 'asset'
      },
      max: {
        type: Number,
        default: 10
      },
      allowFile: {
        type: Boolean,
        default: true
      },
      disabled: {
        type: Boolean,
        default: false
      },
      buttonOnTop: {
        type: Boolean,
        default: false
      }
    },
    data: function () {
      return {
        handle: '.asset__handle', // Drag handle override
        hoverable: true
      }
    },
    computed: {
      addLabel: function () {
        const itemNames = this.itemLabel + 's'
        return 'Attach ' + itemNames
      },
      items: {
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
      remainingItems: function () {
        return Math.max(0, this.max - this.items.length)
      },
      ...mapState({
        selectedMedias: state => state.mediaLibrary.selected
      })
    }
  }
</script>

<style lang="scss" scoped>
  .assetField {
    display: block;
    border-radius: 2px;
    border: 1px solid $color__border;
    overflow-x: hidden;
  }

  .assetField__trigger {
    padding: 10px;
    position: relative;
    border-top: 1px solid $color__border--light;

    &:first-child {
      border-top:0 none
    }
  }

  .assetField__note {
    color: $color__text--light;
    float: right;
    position: absolute;
    bottom: 18px;
    right: 15px;
    display: none;

    @include breakpoint('small+') {
      display: inline-block;
    }

    @include breakpoint('medium') {
      display: none;
    }
  }

  .asset {
    display: flex;
    flex-direction: row;
    border-bottom: 1px solid $color__border--light;
    background-color: $color__background;

    &:last-child {
      border-bottom: 0 none;
    }
  }

  .asset__handle {
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

  .asset__handle:hover .asset__handle--drag:before {
    background: dragGrid__bg($color__drag_bg--hover);
  }

  .asset__handle--drag {
    position: relative;
    width: 10px;
    height: 52px;
    transition: background 250ms ease;
    @include dragGrid($color__drag, $color__drag_bg);
  }

  .asset__content {
    flex-grow: 1;
    max-width:calc(100% - 12px);
  }
</style>
