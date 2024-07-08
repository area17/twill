<template>
  <div>
    <div class="dam-filters" ref="filterDropdowns">
      <a17-button
        variant="ghost"
        class="dam-filters__mobileToggle"
        :aria-expanded="filtersModalOpen ? 'true' : 'false'"
        @click="openFiltersModal"
        ref="openFiltersBtn"
        >{{ $trans('dam.filter-by', 'Filter by')
        }}<span v-svg symbol="filter"></span
      ></a17-button>
      <div
        :class="[
          'dam-filters__modal',
          { 'dam-filters__modal--open': filtersModalOpen }
        ]"
        :role="isMobile ? 'dialog' : null"
        :aria-modal="isMobile ? 'true' : null"
        :aria-labelledby="isMobile ? 'filtersModalHeading' : null"
      >
        <div class="dam-filters__modal-header">
          <h3 class="f--regular" id="filtersModalHeading">
            {{ $trans('dam.filter-by', 'Filter by') }}
          </h3>
          <a17-button
            :aria-label="$trans('dam.close', 'Close')"
            variant="ghost"
            class="dam-filters__close"
            @click="closeFiltersModal"
            ref="closeFiltersBtn"
            ><span v-svg symbol="close"></span
          ></a17-button>
        </div>
        <div class="dam-filters__modal-content">
          <a17-dam-filter-dropdown
            v-for="(item, i) in filtersToDisplay"
            :key="i"
            :label="item.label"
            :items="item.items"
            :name="item.name"
            :hasNestedItems="item.hasNestedItems"
            :hasSearch="item.searchable"
            :searchEndpoint="searchEndpoint"
            :advanced="item.advanced"
            ref="filterDropdown"
            :isMobile="isMobile"
            @filtersApplied="updateAppliedFilters"
          >
          </a17-dam-filter-dropdown>

          <a17-button variant="ghost" @click="toggleAdvanced">
            <template v-if="!showAdvanced">{{
              $trans('dam.show-advanced', 'Show advanced')
            }}</template>
            <template v-else>{{
              $trans('dam.hide-advanced', 'hide advanced')
            }}</template>
          </a17-button>
        </div>
        <div class="dam-filters__modal-footer">
          <a17-button variant="ghost" @click="clearFilters">{{
            $trans('dam.clear', 'Clear')
          }}</a17-button>
          <a17-button variant="ghost" @click="applyFilters">{{
            $trans('dam.apply', 'Apply')
          }}</a17-button>
        </div>
      </div>
    </div>
    <div
      class="dam-filters dam-filters__applied"
      :class="{
        'dam-filters__applied--visible': flattenedAppliedFilters.length
      }"
    >
      <div class="dam-filters__header">
        <h2 id="appliedHeading">
          {{ $trans('nav.applied-filters', 'Applied filters') }}
        </h2>
        <div class="dam-filters-action">
          <button class="f--link-underlined--o" @click="resetFilters">
            {{ $trans('nav.reset', 'Reset') }}
          </button>
          <button class="f--link-underlined--o" @click="applyAppliedFilters">
            {{ $trans('nav.apply', 'Apply') }}
          </button>
        </div>
      </div>
      <a17-checkboxgroup
        :ariaLabelledby="'appliedHeading'"
        :name="'appliedFilters'"
        :options="flattenedAppliedFilters"
        ref="appliedCheckboxGroup"
        @change="handleAppliedFiltersChange"
      ></a17-checkboxgroup>
    </div>
  </div>
</template>

<script>
  import {mapGetters, mapState} from 'vuex'
  import A17DamFilterDropdown from '@/components/dam/DamFilterDropdown.vue'
  import {MEDIA_LIBRARY} from "@/store/mutations";

  export default {
    name: 'DamFilters',
    components: {
      'a17-dam-filter-dropdown': A17DamFilterDropdown
    },
    data: function() {
      return {
        appliedFilters: {},
        filtersModalOpen: false,
        isMobile: false,
        showAdvanced: false,
        customColorValue: null
      }
    },
    computed: {
      ...mapGetters(['fieldValueByName']),
      title() {
        // Get the title from the store
        const title = this.fieldValueByName(this.name)
          ? this.fieldValueByName(this.name)
          : ''
        const titleValue =
          typeof title === 'string' ? title : title[this.currentLocale.value]
        return titleValue || this.warningMessage
      },
      userData() {
        return JSON.parse(this.currentUser)
      },
      filtersToDisplay() {
        if (!this.isMobile && !this.showAdvanced) {
          return this.filters.filter(item => item.advanced !== true)
        }

        return this.filters
      },
      flattenedAppliedFilters() {
        let flattenedFilters = []

        for (const dropdownName in this.appliedFilters) {
          if (Array.isArray(this.appliedFilters[dropdownName])) {
            flattenedFilters = flattenedFilters.concat(
              this.appliedFilters[dropdownName]
            )
          } else {
            for (const tagName in this.appliedFilters[dropdownName]) {
              flattenedFilters = flattenedFilters.concat(
                this.appliedFilters[dropdownName][tagName]
              )
            }
          }
        }

        return flattenedFilters
      },
      ...mapState({
        filterData: state => state.mediaLibrary.filterData,
        filters: state => state.mediaLibrary.filters,
        searchEndpoint: state => state.mediaLibrary.filterSearchEndpoint
      })
    },
    watch: {
      filtersModalOpen(newValue) {
        if (newValue) {
          document.documentElement.classList.add('s--modal')
        } else {
          document.documentElement.classList.remove('s--modal')
        }
      }
    },
    methods: {
      openFiltersModal() {
        this.filtersModalOpen = true

        setTimeout(() => {
          this.$refs.closeFiltersBtn.$el.focus()
        }, 10)
      },
      closeFiltersModal() {
        this.filtersModalOpen = false

        setTimeout(() => {
          this.$refs.openFiltersBtn.$el.focus()
        }, 10)
      },
      applyAppliedFilters() {
        const values = []

        // Collect all checked checkbox values
        this.$refs.filterDropdowns
          .querySelectorAll('input[type="checkbox"]:checked')
          .forEach(checkbox => {
            values.push(checkbox.value)
          })

        // Iterate through appliedFilters and remove unmatched values
        for (const key in this.appliedFilters) {
          if (Array.isArray(this.appliedFilters[key])) {
            this.appliedFilters[key] = this.appliedFilters[key].filter(
              filter => {
                return values.includes(filter.value)
              }
            )
          } else {
            for (const deepKey in this.appliedFilters[key]) {
              this.appliedFilters[key][deepKey] = this.appliedFilters[key][deepKey].filter(
                filter => {
                  return values.includes(filter.value)
                }
              )
            }
          }
        }

        this.appliedFilters = { ...this.appliedFilters }
        this.$store.commit(MEDIA_LIBRARY.SET_FILTER_DATA, this.appliedFilters)
      },
      applyFilters() {
        this.$refs.filterDropdown.forEach(el => {
          el.applyFilters()
        })

        this.closeFiltersModal()
      },
      clearFilters() {
        this.$refs.filterDropdown.forEach(el => {
          el.clearFilters()
        })
      },
      submitSearch(formData) {},
      getMediaQuery() {
        if (typeof window !== 'undefined') {
          const mq = getComputedStyle(document.documentElement)
            .getPropertyValue('--breakpoint')
            .trim()
            .replace(/"/g, '')
          this.isMobile = mq === 'xsmall' || mq === 'small'
        }
      },
      toggleAdvanced() {
        this.showAdvanced = !this.showAdvanced
      },
      updateAppliedFilters(newFilters, dropdownName) {
        this.$set(
          this.appliedFilters,
          dropdownName,
          newFilters
        )

        this.$store.commit(MEDIA_LIBRARY.SET_FILTER_DATA, this.appliedFilters)

        // Set applied checkbox values
        this.applyFilterValues()
      },
      handleAppliedFiltersChange(changedFilters) {
        this.$refs.filterDropdowns
          .querySelectorAll('input[type="checkbox"]')
          .forEach(checkbox => {
            const initialVal = checkbox.checked
            checkbox.checked = changedFilters.includes(checkbox.value)

            if (checkbox.checked !== initialVal) {
              checkbox.dispatchEvent(new Event('change'))
            }
          })
      },
      resetFilters() {
        this.appliedFilters = {}
        this.clearFilters()
        this.$emit('resetFilters')
        this.clearQueryStrings()
      },
      clearQueryStrings(){
        const url  = new URL(window.location.href)
        url.search = ''
        window.history.replaceState({}, document.title, url.pathname + url.hash)
        this.$store.commit(MEDIA_LIBRARY.SET_FILTER_DATA, {})
      },
      bindInputs() {
        const inputs = this.$el.querySelectorAll('input')
        inputs.forEach(input => {
          input.addEventListener('change', e => {
            if (input.type === 'checkbox') {
              // Find other checkboxes with the same value
              const sameValueCheckboxes = this.$el.querySelectorAll(
                `input[type="checkbox"][value="${input.value}"]`
              )
              // Toggle checked status for each checkbox
              sameValueCheckboxes.forEach(checkbox => {
                checkbox.checked = input.checked
              })
            }
          })
        })
      },
      setAppliedFilters() {
        const appliedFilters = {}
        for (const key in this.filterData) {
          if (Array.isArray(this.filterData[key])) {
            const matchedItems = [];
            const filter = this.filters.find(filter => filter.name === key);
            this.filterData[key].forEach(value => {
              let item
              let isCustomColor = false
              if (key === 'colors') {
                isCustomColor = value.includes('custom-')
                item = filter.items.find(item => {
                  return isCustomColor ? item.label === 'Custom' : item.hex === value
                })
              } else {
                item = filter.items.find(item => item.value.toString() === value)
              }

              const newItem = {
                value: `${key}-${item.value}`,
                label: item.label
              };

              if (key === 'colors') {
                newItem.hex = isCustomColor ? value : item.hex
              }
              matchedItems.push(newItem)

              if (isCustomColor) {
                this.customColorValue = value.replace('custom-', '')
              }
            })

            appliedFilters[key] = matchedItems
          } else {
            const matchedItems = {}
            const filters = this.filters.find(filter => filter.name === key);
            for (const deepKey in this.filterData[key]) {
              const filter = filters.items.find(filter => filter.name === deepKey)
              const newFilters = [];
              this.filterData[key][deepKey].forEach(value => {
                const item = filter.items.find(item => item.value.toString() === value)

                newFilters.push({
                  value: `${deepKey}-${item.value}`,
                  label: item.label
                });
              })
              matchedItems[deepKey] = newFilters;
            }

            appliedFilters[key] = matchedItems
          }
        }

        this.appliedFilters = appliedFilters
      },
      applyFilterValues() {
        const filterValues = this.flattenedAppliedFilters.map(filter => {
          return filter.value
        })
        this.$refs.appliedCheckboxGroup.updateValue(filterValues)
      }
    },
    mounted() {
      this.getMediaQuery()
      this.bindInputs()

      this.setAppliedFilters()
      this.applyFilterValues()

      window.addEventListener('resize', this.getMediaQuery)

      this.$nextTick(() => {
        if (this.customColorValue) {
          this.$refs.filterDropdown.find(component => component.customColorCheckbox)
            .$refs.colorField.updateValue(this.customColorValue)
        }
      })
    },
    beforeDestroy() {
      window.removeEventListener('resize', this.getMediaQuery)
    }
  }
</script>

<style lang="scss">
  .input-wrapper-appliedFilters {
    ul {
      @include breakpoint('medium+') {
        display: flex;
        flex-direction: row wrap;
        gap: rem-calc(20);
      }
    }
  }
</style>

<style lang="scss" scoped>
  .dam-filters {
    padding: rem-calc(16);
    display: flex;
    flex-flow: row wrap;
    gap: rem-calc(20);
    background: $color__black--5;
    border-bottom: 1px solid $color__black--10;

    @include breakpoint('medium+') {
      padding: rem-calc(20);
    }
  }

  .dam-filters__mobileToggle {
    display: flex;
    flex-flow: row;
    align-items: center;
    border: none;
    background: $color__border;

    .icon {
      margin-left: rem-calc(10);
    }

    @include breakpoint('medium+') {
      display: none;
    }
  }

  .dam-filters__modal {
    position: fixed;
    inset: 0;
    background: $color__light;
    z-index: 100;
    height: 100svh;
    overflow-y: auto;
    display: flex;
    flex-flow: column;
    opacity: 0;
    visibility: hidden;
    transition: all 300ms ease;

    &--open {
      opacity: 1;
      visibility: visible;
    }

    @include breakpoint('medium+') {
      position: relative;
      inset: unset;
      background: none;
      z-index: unset;
      height: auto;
      overflow: visible;
      display: block;
      opacity: 1;
      visibility: visible;
      transition: none;
    }
  }

  .dam-filters__modal-header {
    display: flex;
    flex-flow: row;
    justify-content: space-between;
    height: rem-calc(68);
    padding: 0 rem-calc(16);
    align-items: center;
    background: $color__border;
    border-bottom: 1px solid $color__modal--header;
    flex-shrink: 0;

    .f--regular {
      font-weight: 600;
    }

    .button {
      padding: 0;
      border: none;
    }

    @include breakpoint('medium+') {
      display: none;
    }
  }

  .dam-filters__modal-content {
    background: $color__light;
    overflow-y: auto;
    height: 100%;

    > .button {
      @include breakpoint('small-') {
        display: none;
      }
    }

    @include breakpoint('medium+') {
      background: none;
      display: flex;
      flex-flow: row wrap;
      gap: rem-calc(20);
      overflow-y: visible;
      height: auto;
    }
  }

  .dam-filters__modal-footer {
    display: flex;
    flex-flow: row;
    justify-content: flex-end;
    height: rem-calc(68);
    padding: 0 rem-calc(16);
    align-items: center;
    background: $color__border;
    border-top: 1px solid $color__modal--header;
    gap: rem-calc(16);
    margin-top: auto;
    flex-shrink: 0;

    @include breakpoint('medium+') {
      display: none;
    }
  }

  .dam-filters__header {
    width: 100%;
    display: flex;
    flex-direction: row;
    gap: rem-calc(16);
    justify-content: space-between;
  }

  .dam-filters-action {
    display: flex;
    flex-direction: row;
    gap: rem-calc(16);

    button {
      @include btn-reset;
      color: $color__link;
      border: none;
      padding: 0;
    }
  }

  .dam-filters__applied {
    display: none;

    &--visible {
      display: flex;
    }
  }

  .input-wrapper-appliedFilters {
    margin-top: 0;
  }
</style>
