<template>
  <div :class="formClasses">
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

      <div class="filter__controls dam-filters__controls">
        <input
          v-if="showSearch"
          v-model="searchValue"
          type="search"
          class="form__input form__input--small"
          :placeholder="$trans('filter.search-placeholder')"
          @keyup.enter="submitSearch"
        />

        <a17-button
          v-if="showFiltersToggle"
          class="filter__toggle"
          variant="ghost"
          type="button"
          :aria-expanded="expanded ? 'true' : 'false'"
          @click="expanded = !expanded"
        >
          {{ $trans('filter.toggle-label') }}
          <span v-svg symbol="dropdown_module"></span>
        </a17-button>

        <div v-if="showTopActions" class="dam-filters__actions">
          <a17-button
            v-if="showCreate"
            variant="validate"
            size="small"
            :href="skipCreateModal ? createUrl : null"
            :el="skipCreateModal ? 'a' : 'button'"
            @click="openModal"
          >
            {{ uploadBtnLabel && uploadBtnLabel.trim()  ? uploadBtnLabel : $trans('listing.add-new-button') }}
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

    <div v-if="showFiltersSection" class="filter__more">
      <div class="filter__moreHidden">
        <div class="dam-filter-wrapper__filters-row">
          <div :class="filtersContentClasses">
            <slot v-if="$slots.filters" name="filters">
              <a17-dam-filters
                ref="damFilters"
                @applyFilters="handleFiltersApply"
                @resetFilters="handleFiltersReset"
              />
            </slot>
            <slot v-else name="hidden-filters">
              <a17-dam-filters
                ref="damFilters"
                @applyFilters="handleFiltersApply"
                @resetFilters="handleFiltersReset"
              />
            </slot>
          </div>

          <div v-if="showInlineActions" class="dam-filters__actions dam-filters__actions--inline">
            <a17-button
              v-if="showCreate"
              variant="validate"
              size="small"
              :href="skipCreateModal ? createUrl : null"
              :el="skipCreateModal ? 'a' : 'button'"
              @click="openModal"
            >
              {{ uploadBtnLabel && uploadBtnLabel.trim()  ? uploadBtnLabel : $trans('listing.add-new-button') }}
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
    </div>
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import A17DamFilters from '@/components/dam/DamFilters.vue'
  import { MEDIA_LIBRARY } from '@/store/mutations'

  export default {
    name: 'A17DamFilterWrapper',
    components: {
      'a17-dam-filters': A17DamFilters
    },

    props: {
      damView: {
        type: String,
        default: 'landing'
      },
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
      showSearch: {
        type: Boolean,
        default: true
      },
      alwaysShowFilters: {
        type: Boolean,
        default: false
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
      },
      uploadBtnLabel: {
        type: String,
        default: () => ''
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
        mediaFilters: state => state.mediaLibrary.filters
      }),
      formClasses () {
        return {
          filter: true,
          'filter--single': !this.hasFiltersContent,
          'filter--withHiddenFilters': this.hasFiltersContent,
          'filter--opened': this.expanded || this.alwaysShowFilters,
          'filter--alwaysVisible': this.alwaysShowFilters,
          'filter--withoutSearch': !this.showSearch
        }
      },

      hasFiltersSlot () {
        return Boolean(this.$slots.filters) || Boolean(this.$slots['hidden-filters'])
      },

      hasFiltersContent () {
        return this.hasFiltersSlot || this.hiddenFilters.length > 0 || this.mediaFilters.length > 0
      },

      showFiltersToggle () {
        return this.hasFiltersContent && !this.alwaysShowFilters
      },

      showFiltersSection () {
        return this.hasFiltersContent && (this.alwaysShowFilters || this.expanded)
      },

      filtersContentClasses () {
        return {
          'dam-filter-wrapper__filters-content': true,
          'dam-filter-wrapper__filters-content--always-visible': this.alwaysShowFilters
        }
      },

      hasActions () {
        return this.showCreate || this.filterLinks.length > 0
      },

      showTopActions () {
        return this.hasActions && !this.alwaysShowFilters
      },

      showInlineActions () {
        return this.hasActions && this.alwaysShowFilters
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

      syncMediaFilters () {
        this.$store.commit(MEDIA_LIBRARY.SET_DAM_STATUS_FILTER, {
          key: 'status',
          value: this.activeStatus
        })
      },

      clearSearch () {
        this.searchValue = ''
        this.$store.commit(MEDIA_LIBRARY.SET_DAM_SEARCH, {})
      },

      handleFiltersReset () {
        this.clearSearch()
        this.applyFilters()
      },
      openModal() {
        this.damView === 'landing' ? this.$root.$refs.damMediaLibrary.open() : this.$root.$refs.editionModal.open()
      },

      handleFiltersApply () {
        this.applyFilters()
      },

      applyFilters () {
        this.syncMediaFilters()
      },

      selectStatus (status) {
        this.activeStatus = status || 'all'
        this.applyFilters()
      },

      clearFilters () {
        this.activeStatus = 'all'
        if (!this.alwaysShowFilters) {
          this.expanded = false
        }
        this.clearSearch()
        this.syncMediaFilters()
        this.$refs.damFilters?.resetFilters?.()
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

  .dam-filters__controls {
    align-items: center;
    display: flex;
    flex: 1 1 auto;
    flex-wrap: wrap;
    gap: rem-calc(12);
    justify-content: flex-end;
    min-width: 0;
  }

  .dam-filters__controls .form__input {
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

  .filter__moreHidden {
    border-top: 1px solid $color__border;
    padding-top: rem-calc(16);
    width: 100%;
  }

  .filter--alwaysVisible .filter__moreHidden {
    border-top: 0;
    padding-top: 0;
  }

  .dam-filter-wrapper__filters-content {
    flex: 1 1 auto;
    min-width: 0;
    width: 100%;
  }

  .dam-filter-wrapper__filters-content--always-visible {
    width: auto;
  }

  .dam-filter-wrapper__filters-row {
    align-items: center;
    display: flex;
    flex-wrap: wrap;
    gap: rem-calc(12);
    width: 100%;
  }

  .dam-filters__actions--inline {
    flex: 0 0 auto;
    justify-content: flex-end;
    margin-left: auto;
    padding-right: rem-calc(8);
  }

</style>
