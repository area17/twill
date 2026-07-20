<template>
  <a17-modal :title="modalTitle" mode="wide" ref="modal" @open="opened">
    <div class="medialibrary">
      <div class="medialibrary__frame">
        <div class="medialibrary__header" ref="form">
          <a17-filter @submit="submitFilter" :clearOption="true" @clear="clearFilters">
            <div slot="navigation" class="medialibrary__header-actions">
              <a17-button variant="validate" size="small" :disabled="selectedMedias.length === 0" @click="attachToProject">
                {{ attachBtnLabel }}
              </a17-button>
            </div>

            <div slot="hidden-filters">
              <a17-vselect 
                class="medialibrary__filter-item" 
                ref="tagFilter" 
                name="tag" 
                :options="tags"
                :placeholder="$trans('media-library.filter-select-label', 'Filter by tag')" 
                :searchable="true" 
                maxHeight="175px"
              />
              <a17-checkbox 
                class="medialibrary__filter-item" 
                ref="unused" 
                name="unused" 
                :initial-value="0" 
                :value="1" 
                :label="$trans('media-library.unused-filter-label', 'Show unused only')"
              />
            </div>
          </a17-filter>
        </div>
        <div class="medialibrary__inner">
          <div class="medialibrary__grid">
            <aside class="medialibrary__sidebar">
              <div class="medialibrary__sidebar-content">
                <div v-if="selectedMedias.length > 0" class="medialibrary__sidebar-selected">
                  <div class="medialibrary__sidebar-header">
                    <h3>{{ selectedMedias.length }} {{ $trans('media-library.selected', 'selected') }}</h3>
                    <button type="button" class="medialibrary__sidebar-clear" @click="clearSelectedMedias">
                      {{ $trans('media-library.clear', 'Clear') }}
                    </button>
                  </div>
                  <ul class="medialibrary__sidebar-list">
                    <li v-for="media in selectedMedias" :key="media.id" class="medialibrary__sidebar-item">
                      <div class="medialibrary__sidebar-thumb">
                        <img :src="media.thumbnail" :alt="media.name" />
                      </div>
                      <span class="medialibrary__sidebar-name">{{ media.name }}</span>
                    </li>
                  </ul>
                </div>
                <div v-else class="medialibrary__sidebar-empty">
                  <p>{{ $trans('media-library.no-media-selected', 'No media selected') }}</p>
                </div>
              </div>
            </aside>
            <div class="medialibrary__list" ref="list">
              <div class="medialibrary__list-items">
                <a17-mediagrid 
                  :items="renderedMediaItems" 
                  :selected-items="selectedMedias" 
                  :used-items="usedMedias" 
                  variant="dam"
                  @change="updateSelectedMedias" 
                  @shiftChange="handleShiftClick"
                />
                <a17-spinner v-if="loading" class="medialibrary__spinner">Loading&hellip;</a17-spinner>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </a17-modal>
</template>

<script>
  import { mapState } from 'vuex'

  import a17Checkbox from '@/components/Checkbox.vue'
  import a17Spinner from '@/components/Spinner.vue'
  import { NOTIFICATION } from '@/store/mutations'
  import FormDataAsObj from '@/utils/formDataAsObj.js'
  import scrollToY from '@/utils/scrollToY.js'

  import api from '../../store/api/media-library'
  import a17Filter from '../Filter.vue'
  import a17MediaGrid from '@/components/media-library/MediaGrid.vue'

  export default {
    name: 'A17DamSelectExistingAssetModal',
    components: {
      'a17-filter': a17Filter,
      'a17-mediagrid': a17MediaGrid,
      'a17-spinner': a17Spinner,
      'a17-checkbox': a17Checkbox
    },
    props: {
      authorized: {
        type: Boolean,
        default: false
      },
      extraMetadatas: {
        type: Array,
        default: () => []
      },
      translatableMetadatas: {
        type: Array,
        default: () => []
      }
    },
    data: function () {
      return {
        loading: false,
        maxPage: 1,
        mediaItems: [],
        selectedMedias: [],
        page: 1,
        tags: [],
        lastScrollTop: 0,
        gridLoaded: false
      }
    },
    computed: {
      modalTitle: function () {
        return this.$trans('dam.add-existing-assets', 'Add existing assets to project')
      },
      attachBtnLabel: function () {
        const count = this.selectedMedias.length
        if (count === 0) return this.$trans('dam.attach-to-project', 'Attach to project')
        return `${this.$trans('dam.attach', 'Attach')} ${count} ${count > 1 ? this.$trans('dam.files', 'files') : this.$trans('dam.file', 'file')} ${this.$trans('dam.to-project', 'to project')}`
      },
      renderedMediaItems: function () {
        return this.mediaItems.map(item => {
          const fileExtension = item.name
            .split('.')
            .pop()
            .toLowerCase()
          const disabled =
            (this.filesizeMax > 0 && item.filesizeInMb > this.filesizeMax) ||
            (this.widthMin > 0 && item.width < this.widthMin) ||
            (this.heightMin > 0 && item.height < this.heightMin)
          
          return {
            ...item,
            fileExtension,
            disabled
          }
        })
      },
      currentTypeObject: function () {
        return this.types.find((type) => type.value === this.type)
      },
      endpoint: function () {
        return this.currentTypeObject ? this.currentTypeObject.endpoint : ''
      },
      usedMedias: function () {
        // We don't really use "usedMedias" in the same way as the CMS uploader here
        return []
      },
      ...mapState({
        type: state => state.mediaLibrary.type,
        types: state => state.mediaLibrary.types,
        projectId: state => state.browser.selected.project,
        filesizeMax: state => state.mediaLibrary.filesizeMax,
        widthMin: state => state.mediaLibrary.widthMin,
        heightMin: state => state.mediaLibrary.heightMin,
        max: state => state.mediaLibrary.max,
        attachBulkProjectAssetsEndpoint: state => state.mediaLibrary.attachBulkProjectAssetsEndpoint,
      })
    },
    methods: {
      open: function () {
        this.$refs.modal.open()
      },
      close: function () {
        this.$refs.modal.hide()
      },
      opened: function () {
        this.page = 1
        this.mediaItems = []
        this.selectedMedias = []
        this.gridLoaded = false
        this.reloadGrid()
      },
      updateSelectedMedias: function (item, shift = false, ctrlKey = false) {
        const id = item.id
        const alreadySelectedMedia = this.selectedMedias.filter(function(
          media
        ) {
          return media.id === id
        })

        // not already selected
        if (alreadySelectedMedia.length === 0) {
          if (this.max === 1 || !shift || ctrlKey) this.clearSelectedMedias()
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
      handleShiftClick: function (item) {
        if (this.selectedMedias.length === 0) {
          this.updateSelectedMedias(item)
          return
        }

        const lastSelected = this.selectedMedias[this.selectedMedias.length - 1]
        const lastIndex = this.mediaItems.findIndex(m => m.id === lastSelected.id)
        const currentIndex = this.mediaItems.findIndex(m => m.id === item.id)

        if (lastIndex === -1 || currentIndex === -1) {
          this.updateSelectedMedias(item)
          return
        }

        const start = Math.min(lastIndex, currentIndex)
        const end = Math.max(lastIndex, currentIndex)
        const toSelect = this.mediaItems.slice(start, end + 1)

        const newSelected = [...this.selectedMedias]
        toSelect.forEach(media => {
          if (!newSelected.find(m => m.id === media.id)) {
            newSelected.push(media)
          }
        })
        this.selectedMedias = newSelected
      },
      reloadGrid: function () {
        if (this.loading) return
        this.loading = true

        const form = this.$refs.form
        const formdata = FormDataAsObj(form) || {}
        formdata.page = this.page
        formdata.type = this.type
        
        // Exclude assets already in the project
        if (this.projectId) {
          formdata.not_in_project = this.projectId
        }

        api.get(this.endpoint, formdata, (resp) => {
          resp.data.items.forEach(item => {
            if (!this.mediaItems.find(media => media.id === item.id)) {
              this.mediaItems.push(item)
            }
          })
          this.maxPage = resp.data.maxPage || 1
          this.tags = resp.data.tags || []
          this.loading = false
          this.listenScrollPosition()
          this.gridLoaded = true
        }, (error) => {
          this.$store.commit(NOTIFICATION.SET_NOTIF, {
            message: error.data.message || 'Error loading media',
            variant: 'error'
          })
          this.loading = false
        })
      },
      submitFilter: function () {
        const self = this
        const el = this.$refs.list
        this.page = 1
        this.mediaItems = []
        this.selectedMedias = []

        if (el.scrollTop === 0) {
          self.reloadGrid()
          return
        }

        scrollToY({
          el,
          offset: 0,
          easing: 'easeOut',
          onComplete: function () {
            self.reloadGrid()
          }
        })
      },
      clearFilters: function () {
        if (this.$refs.tagFilter) this.$refs.tagFilter.value = null
        if (this.$refs.unused) {
          const input = this.$refs.unused.$el.querySelector('input')
          if (input && input.checked) input.click()
        }
        this.submitFilter()
      },
      clearSelectedMedias: function () {
        this.selectedMedias = []
      },
      listenScrollPosition: function () {
        this.$nextTick(function () {
          if (!this.gridLoaded) return
          const list = this.$refs.list
          list.removeEventListener('scroll', this.scrollToPaginate)
          list.addEventListener('scroll', this.scrollToPaginate)
        })
      },
      scrollToPaginate: function () {
        if (!this.gridLoaded) return
        const list = this.$refs.list
        const offset = 10

        if (list.scrollTop > this.lastScrollTop && list.scrollTop + list.offsetHeight > list.scrollHeight - offset) {
          if (this.maxPage > this.page) {
            this.page = this.page + 1
            this.reloadGrid()
          }
        }
        this.lastScrollTop = list.scrollTop
      },
      attachToProject: function () {
        if (this.selectedMedias.length === 0 || !this.projectId) return
        this.loading = true
        const ids = this.selectedMedias.map(m => m.id)
        
        const endpoint = `${this.attachBulkProjectAssetsEndpoint}`
        const params = {
          ids: ids.join(','),
          dam_project: this.projectId
        }

        api.post(endpoint, params, (resp) => {
          this.$store.commit(NOTIFICATION.SET_NOTIF, {
            message: `${ids.length} assets attached to project successfully`,
            variant: 'success'
          })
          
          // Trigger project listing reload for each newly added media
          this.selectedMedias.forEach(media => {
            if (this.$root.reloadDamListing) {
              this.$root.reloadDamListing(media)
            }
          })

          this.close()
          this.loading = false
        }, (error) => {
          this.$store.commit(NOTIFICATION.SET_NOTIF, {
            message: error.data.message || 'Failed to attach assets',
            variant: 'error'
          })
          this.loading = false
        })
      }
    }
  }
</script>

<style lang="scss" scoped>
  $width_sidebar: (default: 290px, small: 250px, xsmall: 200px);

  .medialibrary {
    display: block;
    width: 100%;
    min-height: 100%;
    padding: 0;
    position: relative;
  }

  .medialibrary__frame {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;
    flex-flow: column nowrap;
  }

  .medialibrary__header {
    background: $color__border--light;
    border-bottom: 1px solid $color__border;
    padding: 0 20px;
  }

  .medialibrary__header-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding:20px 0;
    white-space: nowrap;
  }

  .medialibrary__inner {
    position: relative;
    width: 100%;
    overflow: hidden;
    flex-grow: 1;
  }

  .medialibrary__grid {
    display: flex;
    height: 100%;
  }

  .medialibrary__list {
    margin: 0;
    position: absolute;
    top: 0;
    left: 0;
    right: map-get($width_sidebar, default);
    bottom: 0;
    overflow: auto;
    padding: 20px;

    :deep(.mediagrid) {
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
    }

    :deep(.mediagrid__item) {
      width: auto;
      padding-bottom: 0;
      background: $color__light;
      aspect-ratio: 1/1;
    }

    :deep(.mediagrid__button) {
      top: auto;
      left: auto;
      right: auto;
      bottom: auto;
      width: 100%;
      height: 100%;
    }

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

  .medialibrary__sidebar {
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    width: map-get($width_sidebar, default);
    z-index: 75;
    background: $color__border--light;
    border-left: 1px solid $color__border;
    overflow-y: auto;

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

  .medialibrary__sidebar-content {
    padding: 20px;
  }

  .medialibrary__sidebar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;

    h3 {
      font-size: 14px;
      font-weight: 600;
      margin: 0;
    }
  }

  .medialibrary__sidebar-clear {
    background: none;
    border: none;
    color: $color__link;
    cursor: pointer;
    font-size: 12px;
    padding: 0;
    text-decoration: underline;

    &:hover {
      color: $color__darkBlue--hover;
    }
  }

  .medialibrary__sidebar-list {
    list-style: none;
    padding: 0;
    margin: 0;
  }

  .medialibrary__sidebar-item {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
    padding: 5px;
    background: $color__white;
    border: 1px solid $color__border;
    border-radius: 4px;
  }

  .medialibrary__sidebar-thumb {
    width: 40px;
    height: 40px;
    flex-shrink: 0;
    
    img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 2px;
    }
  }

  .medialibrary__sidebar-name {
    font-size: 12px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .medialibrary__sidebar-empty {
    text-align: center;
    color: $color__grey--54;
    margin-top: 40px;
    font-size: 13px;
  }

  .medialibrary__list-items {
    position: relative;
    display: block;
    width: 100%;
    min-height: 100%;
  }

  .medialibrary__spinner {
    margin: 20px auto;
    display: block;
  }
</style>

<style lang="scss">
  .medialibrary__filter-item {
    .vselect {
      min-width: 200px;
    }
  }

  .medialibrary__filter-item.checkbox {
    margin-top: 8px;
    margin-right: 45px !important;
  }
</style>
