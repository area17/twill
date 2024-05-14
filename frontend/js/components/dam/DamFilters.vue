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
            :hasSearch="item.searchable"
            :advanced="item.advanced"
            ref="filterDropdown"
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
          <button class="f--link-underlined--o" @click="applyFilters">
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
  import { mapGetters } from 'vuex'
  import A17DamFilterDropdown from '@/components/dam/DamFilterDropdown.vue'

  export default {
    name: 'A17Medialibrary',
    components: {
      'a17-dam-filter-dropdown': A17DamFilterDropdown
    },
    props: {
      filters: {
        type: Array,
        default: null
      }
    },
    data: function() {
      return {
        appliedFilters: [],
        filtersModalOpen: false,
        isMobile: false,
        showAdvanced: false
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
          flattenedFilters = flattenedFilters.concat(
            this.appliedFilters[dropdownName]
          )
        }

        return flattenedFilters
      }
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
      applyFilters() {
        this.$refs.filterDropdown.forEach(el => {
          el.applyFilters()
        })
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
        // Initialize the applied filters for the specified dropdown
        this.$set(
          this.appliedFilters,
          dropdownName,
          this.appliedFilters[dropdownName] || []
        )

        const appliedFiltersForDropdown = this.appliedFilters[dropdownName]

        // Remove filters that are not present in the new filters array
        this.appliedFilters[dropdownName] = appliedFiltersForDropdown.filter(
          applied => {
            return newFilters.some(filter => filter.value === applied.value)
          }
        )

        // Remove any previously applied filters that are not in the new filters array
        for (let i = appliedFiltersForDropdown.length - 1; i >= 0; i--) {
          const appliedFilter = appliedFiltersForDropdown[i]
          if (
            !newFilters.some(filter => filter.value === appliedFilter.value)
          ) {
            appliedFiltersForDropdown.splice(i, 1)
          }
        }

        // Add new filters that are not already applied
        newFilters.forEach(filter => {
          if (
            !appliedFiltersForDropdown.some(
              applied => applied.value === filter.value
            )
          ) {
            appliedFiltersForDropdown.push(filter)
          }
        })

        // Ensure Vue detects the change in this.appliedFilters
        this.$set(this.appliedFilters, dropdownName, appliedFiltersForDropdown)

        // Set applied checkbox values
        const filterValues = this.flattenedAppliedFilters.map(filter => {
          return filter.value
        })
        this.$refs.appliedCheckboxGroup.updateValue(filterValues)
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
        this.appliedFilters = []
        this.clearFilters()
      },
      bindInputs() {
        // const inputs = this.$el.querySelectorAll('input')
        // inputs.forEach(input => {
        //   input.addEventListener('change', e => {
        //     if (input.type === 'checkbox') {
        //       // Find other checkboxes with the same value
        //       const sameValueCheckboxes = this.$el.querySelectorAll(
        //         `input[type="checkbox"][value="${input.value}"]`
        //       )
        //       // Toggle checked status for each checkbox
        //       sameValueCheckboxes.forEach(checkbox => {
        //         checkbox.checked = input.checked
        //       })
        //     }
        //   })
        // })
      }
    },
    mounted() {
      this.getMediaQuery()
      this.bindInputs()

      window.addEventListener('resize', this.getMediaQuery)
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
