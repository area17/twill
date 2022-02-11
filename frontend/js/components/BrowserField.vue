<template>
  <div class="browserField">
    <div class="browserField__trigger" v-if="buttonOnTop && remainingItems">
      <a17-button type="button" :disabled="disabled" variant="ghost" @click="openBrowser">{{ addLabel }}</a17-button>
      <input type="hidden" :name="name" :value="itemsIds"/>
      <span class="browserField__note f--small"><slot></slot></span>
    </div>
    <table class="browserField__table" v-if="items.length">
      <draggable :tag="'tbody'" v-model="items" :disabled="disabled">
        <a17-browseritem v-for="(item, index) in items" :key="item.endpointType + '_' + item.id" class="item__content"
                         :name="`${name}_${item.id}`" :draggable="!disabled && draggable" :item="item" @delete="deleteItem(index)"
                         :disabled="disabled"
                         :max="max"
                         :showType="endpoints.length > 0" />
      </draggable>
    </table>
    <div class="browserField__trigger" v-if="!buttonOnTop && remainingItems">
      <a17-button type="button" :disabled="disabled" variant="ghost" @click="openBrowser">{{ addLabel }}</a17-button>
      <input type="hidden" :name="name" :value="itemsIds"/>
      <span class="browserField__note f--small"><slot></slot></span>
    </div>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import { BROWSER } from '@/store/mutations'

  import Browseritem from './BrowserItem.vue'
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
      browserNote: {
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
      endpoints: {
        type: Array,
        default: () => []
      },
      draggable: {
        type: Boolean,
        default: true
      },
      max: {
        type: Number,
        default: 10
      },
      wide: {
        type: Boolean,
        default: false
      },
      buttonOnTop: {
        type: Boolean,
        default: false
      },
      disabled: {
        type: Boolean,
        default: false
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
        return this.$trans('fields.browser.add-label', 'Add') + ' ' + this.itemLabel
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
          this.$store.commit(BROWSER.REORDER_ITEMS, {
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
      deleteAll: function () {
        this.$store.commit(BROWSER.DESTROY_ITEMS, {
          name: this.name
        })
      },
      deleteItem: function (index) {
        this.$store.commit(BROWSER.DESTROY_ITEM, {
          name: this.name,
          index: index
        })
      },
      openBrowser: function () {
        this.$store.commit(BROWSER.UPDATE_BROWSER_CONNECTOR, this.name)
        if (this.endpoints.length > 0) {
          this.$store.commit(BROWSER.UPDATE_BROWSER_ENDPOINTS, this.endpoints)
        } else {
          this.$store.commit(BROWSER.DESTROY_BROWSER_ENDPOINTS)
          this.$store.commit(BROWSER.UPDATE_BROWSER_ENDPOINT, {
            value: this.endpoint,
            label: this.name
          })
        }
        this.$store.commit(BROWSER.UPDATE_BROWSER_MAX, this.max)
        this.$store.commit(BROWSER.UPDATE_BROWSER_TITLE, this.browserTitle)
        this.$store.commit(BROWSER.UPDATE_BROWSER_NOTE, this.browserNote)

        if (this.wide) {
          this.$root.$refs.browserWide.open(this.endpoints.length <= 0)
        } else {
          this.$root.$refs.browser.open(this.endpoints.length <= 0)
        }
      }
    },
    beforeDestroy: function () {
      this.deleteAll()
    }
  }
</script>

<style lang="scss" scoped>

  .browserField {
    // width: 100%;
    display: block;
    border-radius: 2px;
    border: 1px solid $color__border;
    overflow-x: hidden;
    background: $color__background;
  }

  .browserField__trigger {
    padding: 10px;
    position: relative;
    border-top: 1px solid $color__border--light;

    &:first-child {
      border-top: 0 none
    }
  }

  .browserField__note {
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

  .browserField__table {
    // overflow: hidden;
    width: 100%;
    border-collapse: collapse;
    border-spacing: 0;
  }
</style>
