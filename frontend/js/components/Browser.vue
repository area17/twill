<template>
  <div class="browser">
    <div class="browser__frame">
      <div class="browser__header" ref="form">
        <a17-filter @submit="submitFilter"></a17-filter>
      </div>

      <div class="browser__inner">
        <div class="browser__list" ref="list">
          <a17-medialist :items="fullItems" :selectedItems="selectedItems" @change="updateSelectedItems"></a17-medialist>
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

  import a17Filter from './Filter.vue'
  import a17MediaList from './media-library/MediaList.vue'
  import FormDataAsObj from '@/utils/formDataAsObj.js'

  export default {
    name: 'A17browser',
    components: {
      'a17-filter': a17Filter,
      'a17-medialist': a17MediaList
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
        fullItems: [],
        selectedItems: [],
        page: this.initialPage
      }
    },
    computed: {
      ...mapState({
        max: state => state.browser.max,
        endpoint: state => state.browser.endpoint,
        browserTitle: state => state.browser.title
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
          // TEMP : randomize ID, name and SRC for demo purpose
          resp.data.forEach(function (item) {
            item.id = Math.round(Math.random() * 999999)
            item.name = 'image_' + Math.round(Math.random() * 999999) + '.jpg'
          })

          // add items here
          self.fullItems.push(...resp.data)

          // re-listen for scroll position
          self.$nextTick(function () {
            list.addEventListener('scroll', () => self.scrollToPaginate())
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
        const maxPage = 20

        if (list.scrollTop + list.offsetHeight > list.scrollHeight - 50) {
          list.removeEventListener('scroll', () => self.scrollToPaginate())

          if (maxPage > this.page) {
            this.page = this.page + 1

            this.reloadList()
          }
        }
      },
      saveAndClose: function () {
        this.$store.commit('saveSelectedItems', this.selectedItems)
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
  @import "../../scss/setup/variables.scss";
  @import "../../scss/setup/colors.scss";
  @import "../../scss/setup/mixins.scss";

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
    padding:10px;
  }

</style>
