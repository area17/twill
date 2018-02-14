<template>
  <div class="medialibrary">
    <div class="medialibrary__frame">
      <div class="medialibrary__header" ref="form">
        <a17-filter @submit="submitFilter" :clearOption="true" @clear="clearFilters">
          <ul class="secondarynav secondarynav--desktop" slot="navigation" v-if="types.length">
            <li class="secondarynav__item" v-for="navType in types" :class="{ 's--on': type === navType.value, 's--disabled' : type !== navType.value && strict }">
              <a href="#" @click.prevent="updateType(navType.value)"><span class="secondarynav__link">{{ navType.text }}</span><span v-if="navType.total > 0" class="secondarynav__number">({{ navType.total }})</span></a>
            </li>
          </ul>

          <div class="secondarynav secondarynav--mobile secondarynav--dropdown" slot="navigation">
            <a17-dropdown ref="secondaryNavDropdown" position="bottom-left" width="full" :offset="0">
              <a17-button class="secondarynav__button" variant="dropdown-transparent" size="small" @click="$refs.secondaryNavDropdown.toggle()" v-if="selectedType">
                <span class="secondarynav__link">{{ selectedType.text }}</span><span class="secondarynav__number">{{ selectedType.total }}</span>
              </a17-button>
              <div slot="dropdown__content">
                <ul>
                  <li v-for="navType in types" class="secondarynav__item">
                    <a href="#" v-on:click.prevent="updateType(navType.value)"><span class="secondarynav__link">{{ navType.text }}</span><span class="secondarynav__number">{{ navType.total }}</span></a>
                  </li>
                </ul>
              </div>
            </a17-dropdown>
          </div>

          <div slot="hidden-filters">
            <a17-vselect class="medialibrary__filter-item" ref="filter" name="tag" :options="tags" placeholder="Filter by tag" :toggleSelectOption="true"></a17-vselect>
          </div>
        </a17-filter>
      </div>

      <div class="medialibrary__inner">
        <div class="medialibrary__grid">
          <aside class="medialibrary__sidebar">
            <a17-mediasidebar :medias="selectedMedias" @clear="clearSelectedMedias" @delete="deleteSelectedMedias"></a17-mediasidebar>
          </aside>
          <footer class="medialibrary__footer" v-if="selectedMedias.length && showInsert && connector">
            <a17-button variant="action" @click="saveAndClose">{{ selectedMedias.length > 1 ? btnMultiLabel : btnLabel }}</a17-button>
          </footer>
          <div class="medialibrary__list" ref="list">
            <a17-uploader @loaded="addMedia" @clear="clearSelectedMedias" :type="type"></a17-uploader>
            <div class="medialibrary__list-items">
              <a17-itemlist :items="fullMedias" :selectedItems="selectedMedias" @change="updateSelectedMedias" v-if="type === 'file'"></a17-itemlist>
              <a17-mediagrid :medias="fullMedias" :selectedMedias="selectedMedias" @change="updateSelectedMedias" @shiftChange="updateSelectedMedias" v-else></a17-mediagrid>
              <a17-spinner v-if="loading" class="medialibrary__spinner">Loading&hellip;</a17-spinner>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import api from '../../store/api/media-library'

  import a17MediaSidebar from './MediaSidebar.vue'
  import a17Filter from '../Filter.vue'
  import a17Uploader from './Uploader.vue'
  import a17MediaGrid from './MediaGrid.vue'
  import a17ItemList from '../ItemList.vue'
  import a17Spinner from '@/components/Spinner.vue'

  import scrollToY from '@/utils/scrollToY.js'

  import FormDataAsObj from '@/utils/formDataAsObj.js'

  export default {
    name: 'A17Medialibrary',
    components: {
      'a17-filter': a17Filter,
      'a17-mediasidebar': a17MediaSidebar,
      'a17-uploader': a17Uploader,
      'a17-mediagrid': a17MediaGrid,
      'a17-itemlist': a17ItemList,
      'a17-spinner': a17Spinner
    },
    props: {
      btnLabel: {
        type: String,
        default: 'Insert file'
      },
      btnMultiLabel: {
        type: String,
        default: 'Insert files'
      },
      endpoint: {
        type: String,
        default: ''
      },
      initialPage: {
        type: Number,
        default: 1
      }
    },
    data: function () {
      return {
        loading: false,
        showInsert: true,
        maxPage: 20,
        fullMedias: [],
        selectedMedias: [],
        gridHeight: 0,
        page: this.initialPage,
        tags: [],
        lastScrollTop: 0
      }
    },
    computed: {
      selectedType: function () {
        let self = this
        const navItem = self.types.filter(function (t) {
          return t.value === self.type
        })
        return navItem[0]
      },
      ...mapState({
        connector: state => state.mediaLibrary.connector,
        max: state => state.mediaLibrary.max,
        type: state => state.mediaLibrary.type, // image, video, file
        types: state => state.mediaLibrary.types,
        strict: state => state.mediaLibrary.strict,
        selected: state => state.mediaLibrary.selected
      })
    },
    methods: {
      updateType: function (newType) {
        if (this.strict) return
        if (this.type === newType) return

        this.$store.commit('updateMediaType', newType)
        this.submitFilter()
      },
      addMedia: function (media) {
        let self = this
        // add media in first position of the available media
        self.fullMedias.unshift(media)
        this.$store.commit('incrementMediaTypeTotal', this.type)
        // select it
        self.updateSelectedMedias(media.id)
      },
      updateSelectedMedias: function (id, shift = false) {
        const alreadySelectedMedia = this.selectedMedias.filter(function (media) {
          return media.id === id
        })

        // not already selected
        if (alreadySelectedMedia.length === 0) {
          if (this.max === 1) this.clearSelectedMedias()
          if (this.selectedMedias.length >= this.max && this.max > 0) return

          if (shift && this.selectedMedias.length > 0) {
            const lastSelectedMedia = this.selectedMedias[this.selectedMedias.length - 1]
            let lastSelectedMediaIndex = this.fullMedias.findIndex((media) => media.id === lastSelectedMedia.id)
            let selectedMediaIndex = this.fullMedias.findIndex((media) => media.id === id)
            if (selectedMediaIndex === -1 && lastSelectedMediaIndex === -1) return

            let start = null
            let end = null
            if (lastSelectedMediaIndex < selectedMediaIndex) {
              start = lastSelectedMediaIndex + 1
              end = selectedMediaIndex + 1
            } else {
              start = selectedMediaIndex
              end = lastSelectedMediaIndex
            }

            const selectedMedias = this.fullMedias.slice(start, end)

            selectedMedias.forEach((media) => {
              if (this.selectedMedias.length >= this.max && this.max > 0) return
              const index = this.selectedMedias.findIndex((m) => m.id === media.id)
              if (index === -1) {
                this.selectedMedias.push(media)
              }
            })
          } else {
            const mediaToSelect = this.fullMedias.filter(function (media) {
              return media.id === id
            })

            // Add one media to the selected media
            if (mediaToSelect.length) this.selectedMedias.push(mediaToSelect[0])
          }
        } else {
          // Remove one item from the selected media
          this.selectedMedias = this.selectedMedias.filter(function (media) {
            return media.id !== id
          })
        }
      },
      getFormData: function (form) {
        let data = FormDataAsObj(form)

        if (data) data.page = this.page
        else data = { page: this.page }

        data.type = this.type

        return data
      },
      clearFilters: function () {
        this.$refs.filter.$data.value = null
        this.submitFilter()
      },
      clearSelectedMedias: function () {
        this.selectedMedias.splice(0)
      },
      deleteSelectedMedias: function () {
        this.selectedMedias.forEach(() => {
          this.$store.commit('decrementMediaTypeTotal', this.type)
        })
        this.fullMedias = this.fullMedias.filter((media) => {
          return !this.selectedMedias.includes(media)
        })
        this.selectedMedias.splice(0)
      },
      clearFullMedias: function () {
        this.selectedMedias.splice(0)
        this.fullMedias.splice(0)
      },
      reloadGrid: function () {
        let self = this

        this.loading = true

        const form = this.$refs.form
        const list = this.$refs.list
        const formdata = this.getFormData(form)
        if (this.selected[this.connector]) {
          formdata.except = this.selected[this.connector].map((media) => {
            return media.id
          })
        }

        // see api/media-library for actual ajax
        api.get(this.endpoint, formdata, function (resp) {
          // add medias here
          self.fullMedias.push(...resp.data.items)
          self.maxPage = resp.data.maxPage || 1
          self.tags = resp.data.tags || []
          self.$store.commit('updateMediaTypeTotal', { type: self.type, total: resp.data.total })
          self.loading = false

          // re-listen for scroll position
          self.$nextTick(function () {
            if (self.gridHeight !== list.scrollHeight) {
              list.addEventListener('scroll', self.scrollToPaginate)
            }
          })
        })
      },
      submitFilter: function (formData) {
        const self = this
        const el = this.$refs.list
        // when changing filters, reset the page to 1
        this.page = 1

        this.clearFullMedias()
        this.clearSelectedMedias()

        if (el.scrollTop === 0) {
          self.reloadGrid()
          return
        }

        scrollToY({
          el: el,
          offset: 0,
          easing: 'easeOut',
          onComplete: function () {
            self.reloadGrid()
          }
        })
      },

      scrollToPaginate: function () {
        const list = this.$refs.list
        const offset = 10
        if (list.scrollTop > this.lastScrollTop && list.scrollTop + list.offsetHeight > list.scrollHeight - offset) {
          list.removeEventListener('scroll', this.scrollToPaginate)

          if (this.maxPage > this.page) {
            this.page = this.page + 1
            this.reloadGrid()
          } else {
            this.gridHeight = list.scrollHeight
          }
        }

        this.lastScrollTop = list.scrollTop
      },
      saveAndClose: function () {
        this.$store.commit('saveSelectedMedias', this.selectedMedias)

        this.$parent.close()
      }
    },
    mounted: function () {
      // bind scroll on the feed
      this.reloadGrid()
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  $width_sidebar: (default: 290px, small: 250px, xsmall: 200px);

  .medialibrary {
    display: block;
    width: 100%;
    min-height: 100%;
    padding: 0;
    position:relative;
  }

  .medialibrary__header {
    background:$color__border--light;
    border-bottom:1px solid $color__border;
    padding:0 20px;

    @include breakpoint(small-) {
      /deep/ .filter__inner {
        flex-direction: column;
      }

      /deep/ .filter__search {
        padding-top: 10px;
      }

      .secondarynav {
        padding-bottom: 10px;
      }

    }
  }

  .medialibrary__frame {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display:flex;
    flex-flow: column nowrap;
  }

  .medialibrary__inner {
    position: relative;
    width: 100%;
    overflow: hidden;
    flex-grow: 1;
  }

  .medialibrary__footer {
    position: absolute;
    right: 0;
    z-index: 76;
    bottom: 0;
    width: map-get($width_sidebar, default); // fixed arbitrary width
    color: $color__text--light;
    padding:10px;
    overflow:hidden;
    background:$color__border--light;
    border-top:1px solid $color__border;

    > button {
      display: block;
      width: 100%;
    }

    @include breakpoint(small) {
      width: map-get($width_sidebar, small);
    }

    @include breakpoint(xsmall) {
      width: map-get($width_sidebar, xsmall);
    }

    @media screen and (max-width: 550px) {
      width: 100%;
    }
  }

  .medialibrary__sidebar {
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    width: map-get($width_sidebar, default); // fixed arbitrary width
    padding: 0 0 90px 0;
    z-index: 75;
    background:$color__border--light;
    overflow: auto;

    @include breakpoint(small) {
      width: map-get($width_sidebar, small);
    }

    @include breakpoint(xsmall) {
      width: map-get($width_sidebar, xsmall);
    }

    @media screen and (max-width: 550px) {
      display: none;
    }
  }

  .medialibrary__list {
    margin: 0;
    position: absolute;
    top: 0;
    left: 0;
    right:0;
    bottom: 0;
    overflow: auto;
    padding:10px;
  }

  .medialibrary__list-items {
    position: relative;
    display: block;
    width: 100%;
    min-height: 100%;
  }

  .medialibrary__filter-item {
    /deep/ .vselect {
      min-width: 200px;
    }
  }
  /* with a sidebar visible */
  .medialibrary__list {
    right:map-get($width_sidebar, default);

    @include breakpoint(small) {
      right: map-get($width_sidebar, small);
    }

    @include breakpoint(xsmall) {
      right: map-get($width_sidebar, xsmall);
    }

    @media screen and (max-width: 550px) {
      right: 0;
    }
  }

</style>
