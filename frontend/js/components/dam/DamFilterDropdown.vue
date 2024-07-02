<template>
  <div class="dam-filters__wrap">
    <a17-button
      :class="['dam-filters__toggle', { 'dam-filters__toggle--open': isOpen }]"
      :aria-expanded="isOpen ? 'true' : 'false'"
      @click="handleClick"
      ref="trigger"
      >{{ label }}
      <span
        ><span class="count"
          > {{ totalCheckedText }} </span
        ><span v-svg symbol="dropdown_module"></span></span
    ></a17-button>
    <div class="dam-filters__dropdown">
      <div v-if="hasSearch" class="dam-filters__dropdown-search">
        <label :for="`search_${uid}`" class="visually-hidden"
          >{{ $trans('dam.search', 'Search') }} {{ label }}</label
        >
        <div>
          <input
            type="search"
            class="form__input form__input--small"
            name="search"
            :id="`search_${uid}`"
            :value="searchValue"
            :placeholder="placeholder"
          />
          <span v-svg symbol="search"></span>
        </div>
      </div>
      <div
        v-if="items && items.length > 0"
        class="dam-filters__dropdown-content"
        ref="content"
      >
        <template v-for="(subItem) in filterItems">
          <a17-checkboxaccordion
            v-if="subItem.items && subItem.items.length"
            :name="subItem.name"
            :key="subItem.name"
            :options="subItem.items"
            :updateLang="false"
            :min="0"
            :selectedLabel="$trans('dam.selected', 'Selected')"
            ref="checkboxAccordion"
            @selectionChanged="(data) => updateSelectedFilters(data, subItem.name)"
            >{{ subItem.label }}</a17-checkboxaccordion
          >
        </template>
        <a17-checkboxgroup
          v-if="!hasNestedItems"
          :name="name"
          :options="filterItems"
          ref="checkboxGroup"
          @change="updateSelectedFilters"
        ></a17-checkboxgroup>
        <div v-if="customColorCheckbox" class="dam-filters__dropdown-color">
          <a17-colorfield
            name="customColor"
            ref="colorField"
            :disabled="!isCustomColorChecked"
          ></a17-colorfield>
        </div>
        <div v-if="loading" class="loading">
          {{ $trans('dam.loading', 'Loading') }}
        </div>
      </div>
      <div class="dam-filters__dropdown-footer">
        <a17-button variant="ghost" @click="clearFilters">{{
          $trans('dam.clear', 'Clear')
        }}</a17-button>
        <a17-button variant="ghost" @click="applyFilters">{{
          $trans('dam.apply', 'Apply')
        }}</a17-button>
      </div>
    </div>
  </div>
</template>

<script>
  import a17CheckboxAccordion from '@/components/CheckboxAccordion.vue'
  import a17ColorField from '@/components/ColorField.vue'

  export default {
    name: 'A17DamFilterDropdown',
    components: {
      'a17-checkboxaccordion': a17CheckboxAccordion,
      'a17-colorfield': a17ColorField
    },
    props: {
      label: {
        type: String,
        required: true
      },
      name: {
        type: String,
        required: true
      },
      hasSearch: {
        type: Boolean,
        default: false
      },
      items: {
        type: Array,
        default() {
          return []
        }
      },
      totalPages: {
        type: Number,
        default: 1
      },
      isMobile: {
        type: Boolean,
        default: false
      },
      hasNestedItems: {
        type: Boolean,
        default: false
      }
    },
    data: function() {
      return {
        customColorCheckbox: null,
        controller: null,
        loadedItems: [],
        endpoint: null,
        hasTriggeredFetch: false,
        isCustomColorChecked: false,
        isOpen: false,
        loading: false,
        page: 2,
        searchParams: new URLSearchParams(window.location.search),
        searchValue: null,
        selectedFilters: []
      }
    },
    computed: {
      filterItems() {
        return [...this.items, ...this.loadedItems].map(item => {
          const newItem = {...item}
          if (this.hasNestedItems) {
            newItem.items = []
            item.items.forEach(innerItem => {
              const newInnerItem = {...innerItem}
              newInnerItem.value = `${item.name}-${newInnerItem.value}`
              newItem.items.push(newInnerItem)
            })
          } else {
            newItem.value = `${this.name}-${item.value}`
          }
          return newItem
        })
      },
      filterName() {
        return this.label.replace(' ', '-').toLowerCase()
      },
      placeholder() {
        return (
          this.$trans('dam.search', 'Search') + ' ' + this.label.toLowerCase()
        )
      },
      totalChecked() {
        if (this.hasNestedItems) {
          let totalCount = 0;

          for (const key in this.selectedFilters) {
            totalCount += this.selectedFilters[key].length;
          }

          return totalCount;
        } else {
          return this.selectedFilters.length
        }
      },
      uid() {
        return this.name
      },
      totalCheckedText() {
        return this.isMobile ? `${this.totalChecked} ${this.$trans('dam.selected', 'selected')}`
          : (this.totalChecked > 0 ? `(${this.totalChecked})` : '')
      }
    },
    watch: {},
    methods: {
      applyFilters() {
        if (this.isCustomColorChecked) {
          const index = this.selectedFilters.findIndex(filter => filter.value === 'colors-custom')
          this.selectedFilters[index].hex = 'custom-' + this.$refs.colorField.value
        }
        this.$emit('filtersApplied', this.selectedFilters, this.uid)

        if (this.selectedFilters.length > 0) {
          this.searchParams.set(this.filterName, this.selectedFilters.join('|'))
        } else {
          this.searchParams.delete(this.filterName)
        }

        this.searchParams.delete('page')
        this.isOpen = false
      },
      clearFilters() {
        this.selectedFilters = this.hasNestedItems ? {} : [];
        this.$emit('filtersApplied', this.selectedFilters, this.uid)

        if (this.$refs.checkboxGroup) {
          this.$refs.checkboxGroup.updateValue([])
        } else if (this.$refs.checkboxAccordion) {
          this.$refs.checkboxAccordion.forEach(checkboxAccordion => {
            checkboxAccordion.currentValue = null
            setTimeout(() => {
              checkboxAccordion.currentValue = []
            }, 10)
          })
        }

        this.isOpen = false
      },
      async fetchItems(opts) {
        try {
          if (this.controller) {
            this.controller.abort()
          }
        } catch (err) {}

        this.controller = new AbortController()
        const signal = this.controller.signal

        try {
          const response = await fetch(
            `${this.endpoint}?selectedFilters=${new URLSearchParams(
              window.location.search
            ).get(this.filterName)}&page=${this.page++}&search=${
              this.searchValue
            }`,
            { method: 'GET', signal }
          )

          if (!response.ok) {
            throw new Error('Network response was not ok')
          }

          const data = await response.json()

          if (data) {
            this.updateList(data.items, opts)

            if (data.isLastPage) {
              this.loading = false
              this.$refs.content.removeEventListener(
                'scroll',
                this.handleScroll
              )
            }
          }
        } catch (error) {
          console.error(error)
        }
      },
      handleClick() {
        this.isOpen = !this.isOpen
      },
      handleKey(e) {
        if (e.code === 'Escape') {
          this.isOpen = false
          setTimeout(() => {
            this.$refs.trigger.$el.focus()
          }, 10)
        }
      },
      handleScroll() {
        const scrollPosition = this.$refs.content.scrollTop
        const listHeight = this.$refs.content.clientHeight

        if (!this.hasTriggeredFetch && scrollPosition + listHeight) {
          this.hasTriggeredFetch = true
          this.fetchItems({ type: 'loadmore' })
        }
      },
      updateList(data, opts) {
        this.loadedItems = [...this.loadedItems, ...data]

        setTimeout(() => {
          this.hasTriggeredFetch = false
        }, 1)
      },
      updateSelectedFilters(selectedItems, name = null) {
        // Find corresponding object from items array
        const selectedFilters = selectedItems.map(selectedItem => {
          let matchedItem
          if (this.hasNestedItems) {
            this.filterItems.forEach(list => {
              if (!matchedItem && list.items) {
                matchedItem = list.items.find(
                  item => item.value === selectedItem
                )
              }
            })
          } else {
            matchedItem = this.filterItems.find(item => item.value === selectedItem)
          }
          const item = {
            label: matchedItem.label,
            value: matchedItem.value
          }

          if (this.name === 'colors') {
            item.hex = matchedItem.hex
          }
          return item
        })

        if (this.hasNestedItems) {
          this.$set(
            this.selectedFilters,
            name,
            selectedFilters
          )
        } else {
          this.selectedFilters = selectedFilters
        }

        this.isCustomColorChecked =
          this.customColorCheckbox && selectedItems.includes('colors-custom')
      }
    },
    mounted() {
      this.selectedFilters = this.hasNestedItems ? {} : [];

      const colorCheckbox = this.$el.querySelector(
        `input[name="colors"][value="colors-custom"]`
      )

      if (colorCheckbox) {
        this.customColorCheckbox = colorCheckbox
      }

      document.addEventListener('keydown', e => {
        this.handleKey(e)
      })

      if (this.totalPages > 1) {
        this.loading = true
        this.$refs.content.addEventListener('scroll', this.handleScroll, false)
      }
    },
    unmounted() {
      document.removeEventListener('keydown', e => {
        this.handleKey(e)
      })
    }
  }
</script>

<style lang="scss">
  .dam-filters__dropdown {
    .accordion:last-child {
      border-bottom: none;
    }

    .accordion {
      @include breakpoint('small-') {
        border-bottom: 1px solid $color__border;
        background: none;
      }
    }

    .accordion__trigger {
      height: auto;
      padding: rem-calc(18) rem-calc(24) rem-calc(18) rem-calc(0);

      @include breakpoint('small-') {
        background: none !important;

        .icon {
          right: 0;
        }
      }

      @include breakpoint('medium+') {
        padding: rem-calc(12) rem-calc(44) rem-calc(12) rem-calc(16);
      }
    }

    .accordion__list {
      padding: 0 0 rem-calc(8) 0;
      border: none;
    }

    .input {
      margin-top: 0;
      padding-bottom: rem-calc(8);

      @include breakpoint('medium+') {
        padding-bottom: 0;
      }
    }

    .checkBoxGroup {
      @include breakpoint('medium+') {
        padding: 0 rem-calc(16);
      }
    }

    .checkboxGroup__item {
      padding: rem-calc(12) 0;
    }
  }

  .dam-filters__dropdown-color {
    padding: 0 0 rem-calc(12) 0;

    @include breakpoint('medium+') {
      padding: rem-calc(16);
      padding-top: 0;
    }

    .form__field {
      padding: rem-calc(8) rem-calc(16) rem-calc(8) rem-calc(8);
      height: auto;
      line-height: normal;
    }

    .form__field input {
      height: auto;
      line-height: normal;
    }

    .form__field--colorBtn {
      width: rem-calc(16);
      height: rem-calc(16);
    }
  }
</style>

<style lang="scss" scoped>
  .dam-filters__wrap {
    border-top: 1px solid $color__border;

    &:first-child {
      border-top: none;
    }

    &:last-child {
      border-bottom: 1px solid $color__border;
    }

    @include breakpoint('medium+') {
      position: relative;
      border: none;
    }
  }

  .dam-filters__toggle {
    display: flex;
    flex-flow: row;
    align-items: center;
    border: none;
    background: $color__background;
    width: 100%;
    border-radius: 0;
    padding: rem-calc(19) rem-calc(16);
    height: auto;
    line-height: normal;
    justify-content: space-between;

    @include breakpoint('medium+') {
      background: $color__border;
      border-radius: rem-calc(36);
      padding: rem-calc(8) rem-calc(16);
      border: 1px solid transparent;
    }

    .icon {
      margin-left: rem-calc(10);
    }

    &--open {
      @include breakpoint('small-') {
        background: none;
      }

      @include breakpoint('medium+') {
        border: 1px solid $color__black--90;

        .count {
          display: none;
        }
      }
    }

    &--open .icon {
      transform: rotate(180deg);
    }

    &--open + .dam-filters__dropdown {
      visibility: visible;
      opacity: 1;
      height: auto;

      @include breakpoint('small-') {
        max-height: 100svh;
      }
    }
  }

  .dam-filters__dropdown {
    padding: 0 rem-calc(16);
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.25s linear;

    @include breakpoint('medium+') {
      background: $color__background;
      border-radius: 2px;
      position: absolute;
      border: 1px solid $color__border--light;
      box-shadow: 0px 1px 3.5px 0px rgba(0, 0, 0, 0.3);
      width: auto;
      max-width: rem-calc(320);
      max-height: calc(100svh - 228px);
      z-index: 20;
      margin-top: rem-calc(8);
      visibility: hidden;
      opacity: 0;
      transition: opacity 0.25s linear, visibility 0.25s linear;
      padding: 0;
      height: auto;
      overflow: visible;
      overflow-y: auto;
      display: flex;
      flex-flow: column;
    }
  }

  .dam-filters__dropdown-search {
    padding-bottom: rem-calc(16);
    border-bottom: 1px solid $color__modal--header;
    flex-shrink: 0;

    @include breakpoint('medium+') {
      padding: rem-calc(16);
      border-bottom: 1px solid $color__border--light;
      width: rem-calc(288);
    }

    div {
      position: relative;
    }

    input {
      padding-right: rem-calc(28);
    }

    .icon {
      color: $color__grey--54;
      position: absolute;
      top: rem-calc(8);
      right: rem-calc(8);
    }
  }

  .dam-filters__dropdown-content {
    @include breakpoint('medium+') {
      height: 100%;
      overflow-y: auto;
    }

    .loading {
      display: block;
      width: 100%;
      padding: rem-calc(18) 0;
      text-align: center;
    }
  }

  .dam-filters__dropdown-footer {
    display: none;
    flex-shrink: 0;

    @include breakpoint('medium+') {
      border-top: 1px solid $color__border;
      background: $color__light;
      display: flex;
      gap: rem-calc(16);
      padding: rem-calc(16) rem-calc(20);
      justify-content: flex-end;
    }
  }
</style>
