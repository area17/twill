<template>
  <div class="browser">
    <div class="browser__frame">
      <div class="browser__header" ref="form">
        <a17-filter @submit="submitFilter"></a17-filter>
      </div>

      <div class="browser__inner">
        <div class="browser__list" ref="list">
          <a17-itemlist :items="fullItems" :selectedItems="selectedItems" @change="updateSelectedItems"></a17-itemlist>
        </div>
      </div>
      <div class="browser__footer">
        <a17-button type="button" variant="action" @click="saveAndClose">{{ browserTitle }}</a17-button> <!-- selectedItems.length > 1 ? btnMultiLabel : btnLabel -->
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
    data: function () {
      return {
        maxPage: 20,
        fullItems: [],
        selectedItems: [],
        listHeight: 0,
        page: this.initialPage
      }
    },
    computed: {
      ...mapState({
        connector: state => state.browser.connector,
        max: state => state.browser.max,
        endpoint: state => state.browser.endpoint,
        browserTitle: state => state.browser.title,
        selected: state => state.browser.selected
      })
    },
    methods: {
      updateSelectedItems: function (id) {
        const alreadySelected = this.selectedItems.filter(function (item) {
          return item.id === id
        })

        // not already seelcted
        if (alreadySelected.length === 0) {
          if (this.max === 1) this.clearSelectedItems()
          if (this.selectedItems.length >= this.max && this.max > 0) return

          const itemToSelect = this.fullItems.filter(function (item) {
            return item.id === id
          })

          // Add one item to the selected item
          if (itemToSelect.length) this.selectedItems.push(itemToSelect[0])
        } else {
          // Remove one item from the selected item
          this.selectedItems = this.selectedItems.filter(function (item) {
            return item.id !== id
          })
        }
      },
      getFormData: function (form) {
        let data = FormDataAsObj(form)

        if (data) data.page = this.page
        else data = { page: this.page }

        if (this.selected[this.connector]) {
          data.except = this.selected[this.connector].map((item) => {
            return item.id
          })
        }

        return data
      },
      clearSelectedItems: function () {
        this.selectedItems.splice(0)
      },
      clearFullItems: function () {
        this.selectedItems.splice(0)
        this.fullItems.splice(0)
      },
      reloadList: function () {
        let self = this

        const form = this.$refs.form
        const list = this.$refs.list
        const formdata = this.getFormData(form)

        this.$http.get(this.endpoint, { params: formdata }).then(function (resp) {
          // add items here
          self.fullItems.push(...resp.data['data'])

          // re-listen for scroll position if height changed
          self.$nextTick(function () {
            if (self.listHeight !== list.scrollHeight) {
              self.listHeight = list.scrollHeight
              list.addEventListener('scroll', self.scrollToPaginate)
            }
          })
        }, function (resp) {
          // error callback
        })
      },
      submitFilter: function (formData) {
        // when changing filters, reset the page to 1
        this.page = 1

        this.clearFullItems()
        this.clearSelectedItems()
        this.reloadList()
      },
      scrollToPaginate: function () {
        const list = this.$refs.list

        if (list.scrollTop + list.clientHeight > this.listHeight - 10) {
          list.removeEventListener('scroll', this.scrollToPaginate)

          if (this.maxPage > this.page) {
            this.page = this.page + 1

            this.reloadList()
          }
        }
      },
      saveAndClose: function () {
        this.$store.commit(BROWSER.SAVE_ITEMS, this.selectedItems)
        this.$parent.close()
      }
    },
    mounted: function () {
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
    position:relative;
    flex-grow:1;
  }

  .browser__frame {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display:flex;
    flex-flow: column nowrap;
  }

  .browser__inner {
    position: relative;
    width: 100%;
    overflow: hidden;
    flex-grow: 1;

    &::after {
      content:'';
      position:absolute;
      height:1px;
      bottom:0;
      background-color:$color__border--light;
      left:20px;
      right:20px;
    }
  }

  .browser__header {
    background:$color__border--light;
    padding:0 20px;
  }

  .browser__footer {
    padding: 0;
    width:100%;
    color: $color__text--light;
    padding:20px;
    overflow:hidden;
    background:$color__background;
  }

  .browser__list {
    padding: 0;
    margin: 0;
    position: absolute;
    top: 0;
    left: 0;
    right:0;
    bottom: 0;
    overflow: auto;
    padding:10px 10px 0 10px;

    .itemlist {
      padding-bottom:0;
    }
  }

</style>
