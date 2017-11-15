<template>
  <div class="fileField">
    <table class="fileField__list ">
      <draggable :element="'tbody'" v-model="items">
        <a17-fileitem v-for="(item, index) in items" :key="item.id" class="item__content" :name="`${name}_${item.id}`" :draggable="draggable" :item="item" @delete="deleteItem(index)"></a17-fileitem>
      </draggable>
    </table>
    <div class="fileField__trigger">
      <input type="hidden" :name="name" :value="itemsIds"/>
      <a17-button type="button" variant="ghost" @click="openMediaLibrary(remainingItems)" :disabled="!remainingItems">{{ addLabel }}</a17-button>
      <span class="fileField__note f--small"><slot></slot></span>
    </div>
  </div>
</template>
<script>
  import { mapState, mapGetters } from 'vuex'
  import fileItem from './FileItem.vue'
  import draggableMixin from '@/mixins/draggable'
  import mediaLibraryMixin from '@/mixins/mediaLibrary'

  import draggable from 'vuedraggable'

  export default {
    name: 'A17FileField',
    components: {
      'a17-fileitem': fileItem,
      draggable
    },
    mixins: [draggableMixin, mediaLibraryMixin],
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
          this.$store.commit('reorderSelectedMedias', {
            name: this.name,
            medias: value
          })
        }
      },
      itemsIds: function () {
        if (this.selectedItemsByIds[this.name]) {
          return this.selectedItemsByIds[this.name].join()
        } else {
          return ''
        }
      },
      addLabel: function () {
        return 'Add ' + this.itemLabel
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
        this.$store.commit('destroySelectedMedias', {
          name: this.name
        })
      },
      deleteItem: function (index) {
        this.$store.commit('destroyMediasInSelected', {
          name: this.name,
          index: index
        })
      }
    },
    beforeDestroy: function () {
      this.deleteAll()
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

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
