<template>
  <div class="browserField">
    <table class="browserField__list ">
      <draggable :element="'tbody'" v-model="items">
        <a17-browseritem v-for="(item, index) in items" :key="item.id" class="item__content" :name="`${name}_${item.id}`" :draggable="draggable" :item="item" @delete="deleteItem(index)"></a17-browseritem>
      </draggable>
    </table>
    <div class="browserField__trigger">
      <input type="hidden" :name="name" :value="itemsIds" />
      <a17-button type="button" variant="ghost" @click="openBrowser" :disabled="!remainingItems">{{ addLabel }}</a17-button>
      <span class="browserField__note f--small"><slot></slot></span>
    </div>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'

  import Browseritem from './Browseritem.vue'
  import draggableMixin from '@/mixins/draggable'
  import draggable from 'vuedraggable'

  export default {
    name: 'A17BrowserField',
    components: {
      'a17-browseritem': Browseritem,
      draggable
    },
    mixins: [draggableMixin],
    props: {
      name: {
        type: String,
        required: true
      },
      modalTitle: {
        type: String,
        default: ''
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
        default: 10
      }
    },
    data: function () {
      return {
        handle: '.item__handle' // Drag handle override
      }
    },
    computed: {
      remainingItems: function () {
        return this.max - this.items.length
      },
      addLabel: function () {
        return 'Add ' + this.itemLabel
      },
      browserTitle: function () {
        return this.modalTitle !== '' ? this.modalTitle : this.addLabel
      },
      items: {
        get () {
          if (this.selectedBrowser.hasOwnProperty(this.name)) {
            return this.selectedBrowser[this.name] || []
          } else {
            return []
          }
        },
        set (value) {
          this.$store.commit('reorderSelectedItems', {
            name: this.name,
            items: value
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
      ...mapState({
        selectedBrowser: state => state.browser.selected
      }),
      ...mapGetters([
        'selectedItemsByIds'
      ])
    },
    methods: {
      deleteAll: function (index) {
        this.$store.commit('destroyAllItems', {
          name: this.name
        })
      },
      deleteItem: function (index) {
        this.$store.commit('destroySelectedItem', {
          name: this.name,
          index: index
        })
      },
      openBrowser: function () {
        this.$store.commit('updateBrowserConnector', this.name)
        this.$store.commit('updateBrowserEndpoint', this.endpoint)
        this.$store.commit('updateBrowserMax', this.remainingItems)
        this.$store.commit('updateBrowserTitle', this.browserTitle)
        this.$root.$refs.browser.open()
      }
    },
    beforeDestroy: function () {
      this.deleteAll()
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .browserField {
    // width: 100%;
    display: block;
    border-radius: 2px;
    border: 1px solid $color__border;
    overflow-x: hidden;
  }

  .browserField__trigger {
    padding:10px;
    position:relative;
  }

  .browserField__note {
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

  .browserField__list {
    overflow: hidden;
    width: 100%;
    border-collapse: collapse;
    border-spacing: 0;
  }
</style>
