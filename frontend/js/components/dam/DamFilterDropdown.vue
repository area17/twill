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
          >{{ totalChecked }} {{ $trans('dam.selected', 'selected') }}</span
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
            @input="onSearchInput"
          />
          <span v-svg symbol="search"></span>
        </div>
      </div>
      <div
        v-if="items && items.length > 1"
        class="dam-filters__dropdown-content"
      >
        <template v-for="(subItem, index) in items">
          <a17-checkboxaccordion
            v-if="subItem.items && subItem.items.length"
            :key="index"
            :options="subItem.items"
            :updateLang="false"
            :selectedLabel="$trans('dam.selected', 'Selected')"
            ref="checkboxAccordion"
            @selectionChanged="updateSelectedFilters"
            >{{ subItem.label }}</a17-checkboxaccordion
          >
        </template>
        <a17-checkboxgroup
          v-if="!hasNestedItems(items)"
          :name="label"
          :options="items"
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
      hasSearch: {
        type: Boolean,
        default: false
      },
      items: {
        type: Array,
        default() {
          return []
        }
      }
    },
    data: function() {
      return {
        customColorCheckbox: null,
        isCustomColorChecked: false,
        isOpen: false,
        searchValue: null,
        selectedFilters: []
      }
    },
    computed: {
      uid() {
        return this.label.replace(' ', '-').toLowerCase()
      },
      placeholder() {
        return (
          this.$trans('dam.search', 'Search') + ' ' + this.label.toLowerCase()
        )
      },
      totalChecked() {
        return this.selectedFilters.length
      }
    },
    watch: {},
    methods: {
      applyFilters() {
        console.log('Selected filters: ', this.selectedFilters)
      },
      clearFilters() {
        this.selectedFilters = []

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
      hasNestedItems(items) {
        for (const value of Object.values(items)) {
          if (value && typeof value === 'object' && 'items' in value) {
            return true
          }
        }
        return false
      },
      onSearchInput() {},
      updateSelectedFilters(selectedItems) {
        this.selectedFilters = selectedItems.flat()

        this.isCustomColorChecked =
          this.customColorCheckbox && selectedItems.includes('custom')
      }
    },
    mounted() {
      const colorCheckbox = this.$el.querySelector(
        'input[name="Color"][value="custom"]'
      )

      if (colorCheckbox) {
        this.customColorCheckbox = colorCheckbox
      }

      document.addEventListener('keydown', e => {
        this.handleKey(e)
      })
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

      .count {
        display: none;
      }
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
