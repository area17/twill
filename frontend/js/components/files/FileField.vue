<template>
  <a17-inputframe :error="error" :label="label" :locale="locale" @localize="updateLocale" :size="size" :name="name" :note="fieldNote">
    <div class="fileField">
      <div class="fileField__trigger" v-if="buttonOnTop && remainingItems">
        <input type="hidden" :name="name" :value="itemsIds"/>
        <a17-button type="button" variant="ghost" @click="openMediaLibrary(remainingItems)">{{ addLabel }}</a17-button>
        <span class="fileField__note f--small">{{ note }}</span>
      </div>
      <table class="fileField__list" v-if="items.length">
        <draggable :tag="'tbody'" v-model="items">
          <a17-fileitem v-for="(item, index) in items" :key="item.id" class="item__content" :name="`${name}_${item.id}`" :draggable="isDraggable" :item="item" @delete="deleteItem(index)"></a17-fileitem>
        </draggable>
      </table>
      <div class="fileField__trigger" v-if="!buttonOnTop && remainingItems">
        <input type="hidden" :name="name" :value="itemsIds"/>
        <a17-button type="button" variant="ghost" @click="openMediaLibrary(remainingItems)">{{ addLabel }}</a17-button>
        <span class="fileField__note f--small">{{ note }}</span>
      </div>
    </div>
  </a17-inputframe>
</template>
<script>
  import draggable from 'vuedraggable'
  import { mapGetters,mapState } from 'vuex'

  import draggableMixin from '@/mixins/draggable'
  import inputframeMixin from '@/mixins/inputFrame'
  import localeMixin from '@/mixins/locale'
  import mediaLibraryMixin from '@/mixins/mediaLibrary/mediaLibrary'
  import { MEDIA_LIBRARY } from '@/store/mutations'

  import fileItem from './FileItem.vue'

  export default {
    name: 'A17FileField',
    components: {
      'a17-fileitem': fileItem,
      draggable
    },
    mixins: [draggableMixin, mediaLibraryMixin, localeMixin, inputframeMixin],
    props: {
      type: {
        type: String,
        default: 'file'
      },
      name: {
        type: String,
        required: true
      },
      itemLabel: {
        type: String,
        default: 'Item'
      },
      endpoint: {
        type: String,
        default: ''
      },
      draggable: {
        type: Boolean,
        default: true
      },
      max: {
        type: Number,
        default: 1
      },
      note: {
        type: String,
        default: ''
      },
      fieldNote: {
        type: String,
        default: ''
      },
      filesizeMax: {
        type: Number,
        default: 0
      },
      buttonOnTop: {
        type: Boolean,
        default: false
      }
    },
    data: () => {
      return {
        handle: '.item__handle' // Drag handle override
      }
    },
    computed: {
      remainingItems: function () {
        return this.max - this.items.length
      },
      items: {
        get () {
          if (this.selectedFiles.hasOwnProperty(this.name)) {
            return this.selectedFiles[this.name] || []
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
      isDraggable: function () {
        return this.draggable && this.items.length > 1
      },
      itemsIds: function () {
        if (this.selectedItemsByIds[this.name]) {
          return this.selectedItemsByIds[this.name].join()
        } else {
          return ''
        }
      },
      addLabel: function () {
        return this.$trans('fields.files.add-label', 'Add') + ' ' + this.itemLabel
      },
      ...mapState({
        selectedFiles: state => state.mediaLibrary.selected
      }),
      ...mapGetters([
        'selectedItemsByIds'
      ])
    },
    methods: {
      deleteAll: function (index) {
        this.$store.commit(MEDIA_LIBRARY.DESTROY_MEDIAS, {
          name: this.name
        })
      },
      deleteItem: function (index) {
        this.$store.commit(MEDIA_LIBRARY.DESTROY_SPECIFIC_MEDIA, {
          name: this.name,
          index
        })
      },
      destroyValue: function () {
        this.deleteAll()
      }
    }
  }
</script>

<style lang="scss" scoped>

  .fileField {
    // width: 100%;
    display: block;
    border-radius: 2px;
    border: 1px solid $color__border;
    overflow-x: hidden;
  }

  .fileField__trigger {
    padding: 10px;
    position: relative;
    border-top: 1px solid $color__border--light;

    &:first-child {
      border-top:0 none
    }
  }

  .fileField__note {
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

  .fileField__list {
    overflow: hidden;
    width: 100%;
    border-collapse: collapse;
    border-spacing: 0;
  }
</style>
