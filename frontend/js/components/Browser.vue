<template>
  <div class="browser">
    <div class="browser__frame">
      <div class="browser__header" ref="form">
        <div class="browser__sources"
             v-if="multiSources">
          <a17-vselect class="browser__sources-select"
                       name="sources"
                       :selected="currentEndpoint"
                       :options="endpoints"
                       :required="true"
                       @change="changeBrowserSource"/>
        </div>
        <div class="browser__search">
          <a17-filter @submit="submitFilter"/>
        </div>
      </div>
      <div class="browser__inner">
        <div class="browser__list" ref="list">
          <a17-itemlist :items="fullItems"
                        :keysToCheck="['id', 'edit']"
                        @change="updateSelectedItems"
                        :selectedItems="selectedItems"/>
        </div>
      </div>
      <div class="browser__footer">
        <a17-button type="button" variant="action" @click="saveAndClose">{{ browserTitle }}</a17-button>
        <span class="browser__size-infos">{{ selectedItems.length }} / {{ max }}</span>
      </div>
    </div>
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import { BROWSER } from '@/store/mutations'

  import a17Filter from './Filter.vue'
  import a17ItemList from './ItemList.vue'
  import FormDataAsObj from '@/utils/formDataAsObj.js'

  export default {
    name: 'A17Browser',
    components: {
      'a17-filter': a17Filter,
      'a17-itemlist': a17ItemList
    },
    props: {
      btnLabel: {
        type: String,
        default: 'Insert'
      },
      btnMultiLabel: {
        type: String,
        default: 'Insert files'
      },
      initialPage: {
        type: Number,
        default: 1
      }
    },
    data () {
      return {
        maxPage: 20,
        fullItems: [],
        listHeight: 0,
        page: this.initialPage
      }
    },
    computed: {
      currentEndpoint () {
        return this.endpoints.find(endpoint => endpoint.value === this.endpoint)
      },
      multiSources () {
        return this.endpoints.length > 0
      },
      selectedItems: {
        get () {
          return this.selected[this.connector] || []
        },
        set (items) {
          this.$store.commit(BROWSER.SAVE_ITEMS, items)
        }
      },
      ...mapState({
        connector: state => state.browser.connector,
        max: state => state.browser.max,
        endpoint: state => state.browser.endpoint,
        endpointName: state => state.browser.endpointName,
        endpoints: state => state.browser.endpoints,
        browserTitle: state => state.browser.title,
        selected: state => state.browser.selected
      })
    },
    methods: {
      updateSelectedItems (item) {
        const keysToTest = this.multiSources ? ['id', 'endpointType'] : ['id']
        const availableItem = this.fullItems.some(sItem => keysToTest.every(key => sItem[key] === item[key]))

        if (!availableItem) return

        const alreadySelected = this.selectedItems.some(sItem => keysToTest.every(key => sItem[key] === item[key]))

        // not already selected
        if (!alreadySelected) {
          if (this.max === 1) this.clearSelectedItems()

          // tbd: maybe show an alert to say that max size is reached ?
          if (this.selectedItems.length >= this.max && this.max > 0) return

          this.selectedItems = [...this.selectedItems, item]
        } else {
          // Remove one item from the selected item
          const itemIndex = this.selectedItems.findIndex(sItem => keysToTest.every(key => sItem[key] === item[key]))
          if (itemIndex < 0) return
          const items = [...this.selectedItems]
          items.splice(itemIndex, 1)
          this.selectedItems = items
        }
      },
      getFormData (form) {
        let data = FormDataAsObj(form)

        if (data) {
          data.page = this.page
        } else {
          data = {page: this.page}
        }

        return data
      },
      clearSelectedItems () {
        this.selectedItems = []
      },
      clearFullItems () {
        this.fullItems.splice(0)
      },
      reloadList (hardReload = false) {
        if (hardReload) {
          this.page = 1
        }

        const form = this.$refs.form
        const list = this.$refs.list
        const formdata = this.getFormData(form)

        this.$http.get(this.endpoint, {params: formdata}).then((resp) => {
          // add items here
          if (hardReload) {
            this.clearFullItems()
          }

          this.fullItems.push(...resp.data['data'])

          // re-listen for scroll position if height changed
          this.$nextTick(() => {
            if (this.listHeight !== list.scrollHeight) {
              this.listHeight = list.scrollHeight
              list.addEventListener('scroll', this.scrollToPaginate)
            }
          })
        }, function (resp) {
          // error callback
        })
      },
      submitFilter () {
        // when changing filters, reset the page to 1
        this.page = 1
        this.clearFullItems()
        this.reloadList()
      },
      scrollToPaginate () {
        const list = this.$refs.list

        if (list.scrollTop + list.clientHeight > this.listHeight - 10) {
          list.removeEventListener('scroll', this.scrollToPaginate)

          if (this.maxPage > this.page) {
            this.page = this.page + 1
            this.reloadList()
          }
        }
      },
      saveAndClose () {
        this.$store.commit(BROWSER.SAVE_ITEMS, this.selectedItems)
        this.$parent.close()
      },
      changeBrowserSource (source) {
        this.$store.commit(BROWSER.UPDATE_BROWSER_ENDPOINT, source)
        this.reloadList(true)
      }
    },
    mounted () {
      // bind scroll on the feed
      this.reloadList()
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .browser {
    display: block;
    width: 100%;
    padding: 0;
    position: relative;
    flex-grow: 1;
  }

  .browser__frame {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;
    flex-flow: column nowrap;
  }

  .browser__inner {
    position: relative;
    width: 100%;
    overflow: hidden;
    flex-grow: 1;

    &::after {
      content: '';
      position: absolute;
      height: 1px;
      bottom: 0;
      background-color: $color__border--light;
      left: 20px;
      right: 20px;
    }
  }

  .browser__header {
    background: $color__border--light;
    padding: 0 20px;
    display: flex;
  }

  .browser__sources {
    flex-grow: 2;

    .browser__sources-select {
      padding: 20px 0;
      margin-right: 15px;
    }
  }

  .browser__footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px;
    width: 100%;
    color: $color__text--light;
    overflow: hidden;
    background: $color__background;
  }

  .browser__size-infos {
    @include font-tiny();
    text-align: right;
    float: right;
  }

  .browser__list {
    padding: 10px 10px 0 10px;
    margin: 0;
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    overflow: auto;

    .itemlist {
      padding-bottom: 0;
    }
  }
</style>

<style lang="scss">
  .browser .browser__sources .browser__sources-select {
    .input {
      margin-top: 0;
    }

    .vselect__field .dropdown-toggle {
      height: 35px;
    }
  }
</style>
