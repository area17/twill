<template>
  <div class="dam-listing">
    <div class="dam-listing__list" ref="list">
      <a17-dam-uploader
        ref="uploader"
        v-if="authorized"
        @loaded="addMedia"
        @clear="clearSelectedMedias"
        :type="currentTypeObject"
      />
      <div class="dam-listing__title">
        <h2 class="f--small">{{ listTitle }} ({{ mediaItems.length }})</h2>
        <a17-dropdown
          ref="layoutDropdown"
          position="bottom-right"
          :offset="4"
          :clickable="true"
          :min-width="235"
        >
          <button
            @click.prevent="$refs.layoutDropdown.toggle()"
            :aria-label="$trans('dam.toggle-layout', 'Toggle layout menu')"
          >
            <span
              symbol="preferences"
              class="icon icon--preferences"
              aria-hidden="true"
            >
              <svg>
                <use
                  xmlns:xlink="http://www.w3.org/1999/xlink"
                  xlink:href="#icon--preferences"
                ></use>
              </svg>
            </span>
          </button>
          <template v-slot:dropdown__content>
            <div>
              <a17-checkbox
                :label="$trans('dam.hide-name', 'Hide file name')"
                :initialValue="hideNames"
                value="hide_names"
                inStore="value"
                @change="hideNames = !hideNames"
              />
              <a17-radiogroup
                name="layoutSelection"
                radioClass="layout"
                :label="$trans('dam.layout', 'Layout')"
                :radios="layoutRadios"
                :initialValue="gridView ? 'grid' : 'list'"
                @change="updateLayout"
              />
            </div>
          </template>
        </a17-dropdown>
      </div>
      <div class="dam-listing__list-items">
        <a17-mediagrid
          v-if="gridView"
          :items="renderedMediaItems"
          :selected-items="selectedMedias"
          :used-items="usedMedias"
          :hide-names="hideNames"
          @change="updateSelectedMedias"
          @shiftChange="updateSelectedMedias"
        />
        <a17-itemlist
          v-else
          :items="renderedMediaItems"
          :selected-items="selectedMedias"
          :used-items="usedMedias"
          @change="updateSelectedMedias"
          @shiftChange="updateSelectedMedias"
        />
        <a17-spinner v-if="loading" class="dam-listing__spinner"
          >{{ $trans('dam.loading', 'Loading') }}&hellip;</a17-spinner
        >
      </div>
    </div>
    <a17-dam-sidebar
      v-if="selectedMedias.length"
      :medias="selectedMedias"
      :authorized="authorized"
      :extraMetadatas="extraMetadatas"
      @clear="clearSelectedMedias"
      @delete="deleteSelectedMedias"
      @tagUpdated="reloadTags"
      :type="currentTypeObject"
      :translatableMetadatas="translatableMetadatas"
      @triggerMediaReplace="replaceMedia"
    />
  </div>
</template>

<script>
  import { mapState } from 'vuex'

  import { MEDIA_LIBRARY, NOTIFICATION } from '@/store/mutations'
  import FormDataAsObj from '@/utils/formDataAsObj.js'
  import scrollToY from '@/utils/scrollToY.js'

  import api from '../../store/api/media-library'
  import a17ItemList from '@/components/ItemList.vue'
  import a17MediaGrid from '@/components/media-library/MediaGrid.vue'
  import a17DamUploader from '@/components/dam/Uploader.vue'
  import a17DamSidebar from '@/components/dam/DamSidebar.vue'
  import a17Spinner from '@/components/Spinner.vue'

  export default {
    name: 'A17Medialibrary',
    components: {
      'a17-dam-uploader': a17DamUploader,
      'a17-mediagrid': a17MediaGrid,
      'a17-itemlist': a17ItemList,
      'a17-dam-sidebar': a17DamSidebar,
      'a17-spinner': a17Spinner,
    },
    props: {
      btnLabelSingle: {
        type: String,
        default: function() {
          return window.$trans('media-library.insert', 'Insert')
        }
      },
      btnLabelUpdate: {
        type: String,
        default: function() {
          return window.$trans('media-library.update', 'Update')
        }
      },
      btnLabelMulti: {
        type: String,
        default: function() {
          return window.$trans('media-library.insert', 'Insert')
        }
      },
      initialPage: {
        type: Number,
        default: 1
      },
      authorized: {
        type: Boolean,
        default: false
      },
      showInsert: {
        type: Boolean,
        default: true
      },
      extraMetadatas: {
        type: Array,
        default() {
          return []
        }
      },
      translatableMetadatas: {
        type: Array,
        default() {
          return []
        }
      },
      medias: {
        default: function() {
          return []
        }
      }
    },
    data: function() {
      return {
        loading: false,
        maxPage: 20,
        mediaItems: [],
        selectedMedias: [],
        gridHeight: 0,
        page: this.initialPage,
        tags: [],
        lastScrollTop: 0,
        gridLoaded: false,
        gridView: true,
        hideNames: true
      }
    },
    computed: {
      renderedMediaItems: function() {
        return this.mediaItems.map(item => {
          item.fileExtension = item.name
            .split('.')
            .pop()
            .toLowerCase();
          item.disabled =
            (this.filesizeMax > 0 && item.filesizeInMb > this.filesizeMax) ||
            (this.widthMin > 0 && item.width < this.widthMin) ||
            (this.heightMin > 0 && item.height < this.heightMin)
          return item
        })
      },
      currentTypeObject: function() {
        return this.types.find(type => {
          return type.value === this.type
        })
      },
      endpoint: function() {
        return this.currentTypeObject.endpoint
      },
      btnLabel: function() {
        let type = this.$trans(
          'media-library.types.single.' + this.type,
          this.type
        )

        if (this.indexToReplace > -1) {
          return this.btnLabelUpdate + ' ' + type
        } else {
          if (this.selectedMedias.length > 1) {
            type = this.$trans(
              'media-library.types.multiple.' + this.type,
              this.type
            )
          }

          return this.btnLabelSingle + ' ' + type
        }
      },
      usedMedias: function() {
        return this.selected[this.connector] || []
      },
      selectedType: function() {
        const self = this
        const navItem = self.types.filter(function(t) {
          return t.value === self.type
        })
        return navItem[0]
      },
      canInsert: function() {
        return !this.selectedMedias.some(
          sMedia => !!this.usedMedias.find(uMedia => uMedia.id === sMedia.id)
        )
      },
      layoutRadios: function() {
        return [
          {
            value: 'grid',
            label: this.$trans('dam.grid', 'Grid')
          },
          {
            value: 'list',
            label: this.$trans('dam.list', 'List')
          }
        ]
      },
      listTitle: function() {
        return this.$trans('dam.all-assets', 'All assets')
      },
      ...mapState({
        connector: state => state.mediaLibrary.connector,
        max: state => state.mediaLibrary.max,
        filesizeMax: state => state.mediaLibrary.filesizeMax,
        widthMin: state => state.mediaLibrary.widthMin,
        heightMin: state => state.mediaLibrary.heightMin,
        type: state => state.mediaLibrary.type,
        types: state => state.mediaLibrary.types,
        strict: state => state.mediaLibrary.strict,
        selected: state => state.mediaLibrary.selected,
        indexToReplace: state => state.mediaLibrary.indexToReplace
      })
    },
    watch: {
      type: function() {
        this.clearMediaItems()
        this.gridLoaded = false
      },
      gridView(newVal) {
        localStorage.setItem('gridView', JSON.stringify(newVal))
      },
      hideNames(newVal) {
        // TODO: Set showFileName in store
      }
    },
    methods: {
      replaceMedia: function({ id }) {
        this.$refs.uploader.replaceMedia(id)
      },
      updateType: function(newType) {
        if (this.loading) return
        if (this.strict) return
        if (this.type === newType) return

        this.$store.commit(MEDIA_LIBRARY.UPDATE_MEDIA_TYPE, newType)
        this.submitFilter()
      },
      addMedia: function(media) {
        const index = this.mediaItems.findIndex(function(item) {
          return item.id === media.id
        })

        // Check of the media item exists i.e replacement
        if (index > -1) {
          for (const mediaRole in this.selected) {
            this.selected[mediaRole].forEach((mediaCrop, index) => {
              if (media.id === mediaCrop.id) {
                const crops = []

                for (const crop in mediaCrop.crops) {
                  crops[crop] = {
                    height:
                      media.height === mediaCrop.height
                        ? mediaCrop.crops[crop].height
                        : media.height,
                    name: crop,
                    width:
                      media.width === mediaCrop.width
                        ? mediaCrop.crops[crop].width
                        : media.width,
                    x:
                      media.width === mediaCrop.width
                        ? mediaCrop.crops[crop].x
                        : 0,
                    y:
                      media.height === mediaCrop.height
                        ? mediaCrop.crops[crop].y
                        : 0
                  }
                }

                this.$store.commit(MEDIA_LIBRARY.UPDATE_MEDIAS, {
                  index,
                  media: {
                    ...media,
                    width:
                      media.width === mediaCrop.width
                        ? mediaCrop.width
                        : media.width,
                    height:
                      media.height === mediaCrop.height
                        ? mediaCrop.height
                        : media.height,
                    crops
                  },
                  mediaRole
                })
              }
            })
          }

          this.$set(this.mediaItems, index, media)
          this.selectedMedias.unshift(media)
        } else {
          // add media in first position of the available media
          this.mediaItems.unshift(media)
          this.$store.commit(
            MEDIA_LIBRARY.INCREMENT_MEDIA_TYPE_TOTAL,
            this.type
          )
          // select it
          this.updateSelectedMedias(media.id)
        }
      },
      updateSelectedMedias: function(item, shift = false) {
        const id = item.id
        const alreadySelectedMedia = this.selectedMedias.filter(function(
          media
        ) {
          return media.id === id
        })

        // not already selected
        if (alreadySelectedMedia.length === 0) {
          if (this.max === 1) this.clearSelectedMedias()
          if (this.selectedMedias.length >= this.max && this.max > 0) return

          if (shift && this.selectedMedias.length > 0) {
            const lastSelectedMedia = this.selectedMedias[
              this.selectedMedias.length - 1
            ]
            const lastSelectedMediaIndex = this.mediaItems.findIndex(
              media => media.id === lastSelectedMedia.id
            )
            const selectedMediaIndex = this.mediaItems.findIndex(
              media => media.id === id
            )
            if (selectedMediaIndex === -1 && lastSelectedMediaIndex === -1)
              return

            let start = null
            let end = null
            if (lastSelectedMediaIndex < selectedMediaIndex) {
              start = lastSelectedMediaIndex + 1
              end = selectedMediaIndex + 1
            } else {
              start = selectedMediaIndex
              end = lastSelectedMediaIndex
            }

            const selectedMedias = this.mediaItems.slice(start, end)

            selectedMedias.forEach(media => {
              if (this.selectedMedias.length >= this.max && this.max > 0) return
              const index = this.selectedMedias.findIndex(
                m => m.id === media.id
              )
              if (index === -1) {
                this.selectedMedias.push(media)
              }
            })
          } else {
            const mediaToSelect = this.mediaItems.filter(function(media) {
              return media.id === id
            })

            // Add one media to the selected media
            if (mediaToSelect.length) this.selectedMedias.push(mediaToSelect[0])
          }
        } else {
          // Remove one item from the selected media
          this.selectedMedias = this.selectedMedias.filter(function(media) {
            return media.id !== id
          })
        }
      },
      getFormData: function(form) {
        let data = FormDataAsObj(form)

        if (data) data.page = this.page
        else data = { page: this.page }

        data.type = this.type

        if (Array.isArray(data.unused) && data.unused.length) {
          data.unused = data.unused[0]
        }

        return data
      },
      clearFilters: function() {
        const self = this
        // reset tags
        if (this.$refs.filter) this.$refs.filter.value = null
        // reset unused field
        if (this.$refs.unused) {
          const input = this.$refs.unused.$el.querySelector('input')
          input && input.checked && input.click()
        }

        this.$nextTick(function() {
          self.submitFilter()
        })
      },
      clearSelectedMedias: function() {
        this.selectedMedias.splice(0)
      },
      deleteSelectedMedias: function(mediasIds) {
        let keepSelectedMedias = []
        if (mediasIds && mediasIds.length !== this.selectedMedias.length) {
          keepSelectedMedias = this.selectedMedias.filter(
            media => !media.deleteUrl
          )
        }
        mediasIds.forEach(() => {
          this.$store.commit(
            MEDIA_LIBRARY.DECREMENT_MEDIA_TYPE_TOTAL,
            this.type
          )
        })
        this.mediaItems = this.mediaItems.filter(media => {
          return (
            !this.selectedMedias.includes(media) ||
            keepSelectedMedias.includes(media)
          )
        })
        this.selectedMedias = keepSelectedMedias
        if (this.mediaItems.length <= 40) {
          this.reloadGrid()
        }
      },
      clearMediaItems: function() {
        this.mediaItems.splice(0)
      },
      reloadGrid: function() {
        this.loading = true

        const form = this.$refs.form
        const formdata = this.getFormData(form)

        // see api/media-library for actual ajax
        api.get(
          this.endpoint,
          formdata,
          resp => {
            // add medias here
            resp.data.items.forEach(item => {
              if (!this.mediaItems.find(media => media.id === item.id)) {
                this.mediaItems.push(item)
              }
            })
            this.maxPage = resp.data.maxPage || 1
            this.tags = resp.data.tags || []
            this.$store.commit(MEDIA_LIBRARY.UPDATE_MEDIA_TYPE_TOTAL, {
              type: this.type,
              total: resp.data.total
            })
            this.loading = false
            this.listenScrollPosition()
            this.gridLoaded = true
          },
          error => {
            this.$store.commit(NOTIFICATION.SET_NOTIF, {
              message: error.data.message,
              variant: 'error'
            })
          }
        )
      },
      reloadTags: function(tags = []) {
        this.tags = tags
      },
      submitFilter: function(formData) {
        const self = this
        const el = this.$refs.list
        // when changing filters, reset the page to 1
        this.page = 1

        this.clearMediaItems()
        this.clearSelectedMedias()

        if (el.scrollTop === 0) {
          self.reloadGrid()
          return
        }

        scrollToY({
          el,
          offset: 0,
          easing: 'easeOut',
          onComplete: function() {
            self.reloadGrid()
          }
        })
      },
      listenScrollPosition: function() {
        // re-listen for scroll position
        this.$nextTick(function() {
          if (!this.gridLoaded) return

          const list = this.$refs.list
          if (this.gridHeight !== list.scrollHeight) {
            list.addEventListener('scroll', this.scrollToPaginate)
          }
        })
      },
      scrollToPaginate: function() {
        if (!this.gridLoaded) return

        const list = this.$refs.list
        const offset = 10

        if (
          list.scrollTop > this.lastScrollTop &&
          list.scrollTop + list.offsetHeight > list.scrollHeight - offset
        ) {
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
      updateLayout: function() {
        this.gridView = !this.gridView
      }
    },
    created() {
      if (!this.gridLoaded) this.reloadGrid()

      this.listenScrollPosition()

      // empty selected medias (to avoid bugs when adding)
      this.selectedMedias = []

      // in replace mode : select the media to replace when opening
      if (this.connector && this.indexToReplace > -1) {
        const mediaInitSelect = this.selected[this.connector][
          this.indexToReplace
        ]
        if (mediaInitSelect) {
          this.selectedMedias.push(mediaInitSelect)
        }
      }
    },
  }
</script>

<style lang="scss">
  .dam-listing__title .dropdown__scroller {
    padding: rem-calc(20) rem-calc(16) rem-calc(12) rem-calc(16);

    > div {
      display: flex;
      flex-flow: column;
      gap: rem-calc(24);
    }

    .input__label {
      color: $color__grey--54;
      margin-bottom: rem-calc(12);
    }

    .radioGroup__item {
      padding-top: rem-calc(12);
      padding-bottom: rem-calc(12);
    }
  }

  .dam-listing__list-items {
    .mediagrid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      grid-template-rows: auto;
      height: auto;
      line-height: normal;
      gap: rem-calc(16);

      @include breakpoint('small+') {
        grid-template-columns: repeat(3, 1fr);
        gap: rem-calc(20);
      }

      @include breakpoint('medium+') {
        grid-template-columns: repeat(4, 1fr);
      }

      @include breakpoint('large+') {
        grid-template-columns: repeat(5, 1fr);
      }
    }

    .mediagrid__item {
      width: auto;
      padding-bottom: 0;
      background: $color__light;
      aspect-ratio: 1/1;
    }

    .mediagrid__button {
      position: relative;
      top: auto;
      left: auto;
      right: auto;
      bottom: auto;
      width: 100%;
      height: 100%;
    }
 }
</style>

<style lang="scss" scoped>
  .dam-listing {
    position: relative;
    width: 100%;
    display: flex;
    flex-flow: row;
    overflow: hidden;
  }

  .dam-listing__list {
    container: damlist / inline-size;
    flex: 1 1 auto;
    overflow-y: auto;
    padding: rem-calc(20);

    .itemlist {
      padding: 0;
    }
  }

  .dam-listing__list-items {
    position: relative;
    display: block;
    width: 100%;
    min-height: 100%;
  }

  // @include breakpoint('large+') {
  //   @container damlist (width > 1600px) {
  //     .mediagrid {
  //       grid-template-columns: repeat(6, 1fr);
  //     }
  //   }

  //   @container damlist (width < 880px) {
  //     .mediagrid {
  //       grid-template-columns: repeat(4, 1fr);
  //     }
  //   }
  // }

  .dam__add {
    display: none;
  }

  .dam-listing__title {
    padding: rem-calc(20) 0;
    color: $color__grey--54;
    display: flex;
    flex-flow: row;
    justify-content: space-between;
  }

  .dam-listing__title .dropdown {
    button {
      @include btn-reset;
      color: $color__grey--54;
    }
  }

  .dam-listing__title .dropdown .dropdown__inner .input {
    padding: 0;
  }
</style>
