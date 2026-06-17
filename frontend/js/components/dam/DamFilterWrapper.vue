<template>
  <form :class="formClasses" @submit.prevent="submitFilters">
    <div class="filter__inner">
      <div class="filter__navigation">
        <ul v-if="navigationItems.length" class="secondarynav secondarynav--desktop">
          <li
            v-for="item in navigationItems"
            :key="item.slug || item.name"
            :class="['secondarynav__item', { 's--on': activeStatus === (item.slug || '') }]"
          >
            <a href="#" @click.prevent="selectStatus(item.slug || 'all')">
              <span class="secondarynav__link">{{ item.name || '' }}</span>
              <span v-if="item.number !== null && item.number !== undefined" class="secondarynav__number">
                ({{ item.number }})
              </span>
            </a>
          </li>
        </ul>
      </div>

      <div class="filter__search dam-filters__search">
        <input
          v-model="searchValue"
          type="search"
          class="form__input form__input--small"
          :placeholder="$trans('filter.search-placeholder')"
        />

        <a17-button
          class="filter__toggle"
          variant="ghost"
          type="button"
          :aria-expanded="expanded ? 'true' : 'false'"
          @click="expanded = !expanded"
        >
          {{ $trans('filter.toggle-label') }}
          <span v-svg symbol="dropdown_module"></span>
        </a17-button>

        <div v-if="showCreate || filterLinks.length" class="dam-filters__actions">
          <a17-button
            v-if="showCreate"
            variant="validate"
            size="small"
            :href="skipCreateModal ? createUrl : null"
            :el="skipCreateModal ? 'a' : 'button'"
            @click="handleCreate"
          >
            {{ $trans('listing.add-new-button') }}
          </a17-button>
          <a17-button
            v-for="link in filterLinks"
            :key="link.url || link.label"
            el="a"
            :href="link.url || '#'"
            :download="link.download || ''"
            :rel="link.rel || ''"
            :target="link.target || ''"
            variant="small secondary"
          >
            {{ link.label }}
          </a17-button>
        </div>
      </div>
    </div>

    <div v-show="expanded" class="filter__more">
      <div class="filter__moreInner">
        <div class="dam-filter-wrapper__hidden-filters">
          <slot name="hidden-filters">
            <a17-dam-filters
              ref="damFilters"
              @applyFilters="handleHiddenFiltersApply"
              @resetFilters="handleHiddenFiltersReset"
            />
          </slot>
        </div>
      </div>
    </div>
  </form>
</template>

<script>
  import { mapState } from 'vuex'

  import ACTIONS from '@/store/actions'
  import A17DamFilters from '@/components/dam/DamFilters.vue'
  import { DATATABLE, MEDIA_LIBRARY } from '@/store/mutations'

  export default {
    name: 'A17DamFilterWrapper',
    components: {
      'a17-dam-filters': A17DamFilters
    },

    props: {
      navigationItems: {
        type: Array,
        default: () => []
      },
      initialStatus: {
        type: String,
        default: 'all'
      },
      initialSearch: {
        type: String,
        default: ''
      },
      hiddenFilters: {
        type: Array,
        default: () => []
      },
      showCreate: {
        type: Boolean,
        default: false
      },
      skipCreateModal: {
        type: Boolean,
        default: false
      },
      createUrl: {
        type: String,
        default: ''
      },
      filterLinks: {
        type: Array,
        default: () => []
      },
      additionalTableActions: {
        type: Array,
        default: () => []
      },
      syncDatatable: {
        type: Boolean,
        default: false
      }
    },

    data () {
      return {
        activeStatus: this.initialStatus,
        searchValue: this.initialSearch,
        expanded: false
      }
    },

    computed: {
      ...mapState({
        mediaFilters: state => state.mediaLibrary.filters,
        filterData: state => state.mediaLibrary.filterData
      }),
      formClasses () {
        return {
          filter: true,
          'filter--single': !this.hasHiddenFilters,
          'filter--withHiddenFilters': this.hasHiddenFilters,
          'filter--opened': this.expanded
        }
      },

      hasHiddenFilters () {
        return Boolean(this.$slots['hidden-filters']) || this.hiddenFilters.length > 0 || this.mediaFilters.length > 0
      }
    },

    watch: {
      initialSearch (value) {
        this.searchValue = value
      },
      initialStatus (value) {
        this.activeStatus = value || 'all'
      }
    },

    methods: {
      submitSearch () {
        this.$store.commit(
          MEDIA_LIBRARY.SET_DAM_SEARCH,
          this.searchValue ? { search: this.searchValue } : {}
        )
      },

      getDatatableFilterPayload () {
        const filterPayload = {
          ...this.filterData,
        }

        if (this.activeStatus && this.activeStatus !== 'all') {
          filterPayload.status = this.activeStatus
        } else {
          delete filterPayload.status
        }

        if (this.searchValue) {
          filterPayload.search = this.searchValue
        }

        return filterPayload
      },

      syncDatatableFilters () {
        if (!this.syncDatatable) {
          return
        }

        this.$store.commit(DATATABLE.UPDATE_DATATABLE_PAGE, 1)
        this.$store.commit(DATATABLE.CLEAR_DATATABLE_FILTER)
        this.$store.commit(DATATABLE.UPDATE_DATATABLE_FILTER, this.getDatatableFilterPayload())
      },

      syncMediaFilters () {
        this.$store.commit(MEDIA_LIBRARY.SET_FILTER_ENTRY, {
          key: 'status',
          value: this.activeStatus
        })
      },

      applyDatatableFilters () {
        if (!this.syncDatatable) {
          return
        }

        this.syncDatatableFilters()
        this.$store.dispatch(ACTIONS.GET_DATATABLE)
      },

      clearSearch () {
        this.searchValue = ''
        this.$store.commit(MEDIA_LIBRARY.SET_DAM_SEARCH, {})
      },

      handleHiddenFiltersReset () {
        this.clearSearch()
        this.applyFilters()
      },

      handleHiddenFiltersApply () {
        this.applyFilters()
      },

      applyFilters () {
        if (this.syncDatatable) {
          this.applyDatatableFilters()
          return
        }

        this.syncMediaFilters()
      },

      submitFilters () {
        this.submitSearch()
        this.applyFilters()
        this.expanded = false
      },

      selectStatus (status) {
        this.activeStatus = status || 'all'
        this.applyFilters()
      },

      clearFilters () {
        this.activeStatus = 'all'
        this.expanded = false
        this.clearSearch()
        this.$refs.damFilters?.resetFilters?.()

        if (this.syncDatatable) {
          this.$store.commit(DATATABLE.UPDATE_DATATABLE_PAGE, 1)
          this.$store.commit(DATATABLE.CLEAR_DATATABLE_FILTER)
          this.$store.commit(DATATABLE.UPDATE_DATATABLE_FILTER_STATUS, this.activeStatus)
          this.$store.dispatch(ACTIONS.GET_DATATABLE)
        }
      },

      handleCreate () {
        if (this.skipCreateModal) {
          return
        }

        this.$emit('create')
      }
    }
  }
</script>

<style lang="scss" scoped>
  .filter {
    width: 100%;
  }

  .filter__inner {
    align-items: center;
    display: flex;
    flex-wrap: wrap;
    gap: rem-calc(16);
    justify-content: space-between;
    width: 100%;
  }

  .filter__navigation {
    flex: 1 1 auto;
    min-width: 0;
  }

  .dam-filters__search {
    align-items: center;
    display: flex;
    flex: 1 1 auto;
    flex-wrap: wrap;
    gap: rem-calc(12);
    justify-content: flex-end;
    min-width: 0;
  }

  .dam-filters__search .form__input {
    flex: 0 1 rem-calc(220);
    margin-bottom: 0;
  }

  .dam-filters__actions {
    align-items: center;
    display: flex;
    flex-wrap: wrap;
    gap: rem-calc(8);
  }

  .filter__more {
    width: 100%;
  }

  .filter__moreInner {
    border-top: 1px solid $color__border;
    padding-top: rem-calc(16);
    width: 100%;
  }

  .dam-filter-wrapper__hidden-filters {
    width: 100%;
  }
</style>
