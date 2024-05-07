<template>
  <div class="dam-filters__wrap">
    <a17-button
      :class="['dam-filters__toggle', { 'dam-filters__toggle--open': isOpen }]"
      variant="ghost"
      :aria-expanded="isOpen ? 'true' : 'false'"
      @click="handleClick"
      >{{ label }}<span v-svg symbol="dropdown_module"></span
    ></a17-button>
    <div class="dam-filters__dropdown">
      <div v-if="hasSearch" class="dam-filters__dropdown-search">
        <label :for="`search_${uid}`" class="visually-hidden"
          >Search {{ label }}</label
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
      <div v-if="items && items.length > 1">
        <a17-checkboxaccordion
          v-for="(subItem, index) in items"
          :key="index"
          :options="subItem.items"
          :updateLang="false"
          selected-label="selected"
          name=""
          >{{ subItem.label }}</a17-checkboxaccordion
        >
      </div>
      <div class="dam-filters__dropdown-footer">
        <a17-button variant="ghost">{{
          $trans('dam.clear', 'Clear')
        }}</a17-button>
        <a17-button variant="ghost">{{
          $trans('dam.apply', 'Apply')
        }}</a17-button>
      </div>
    </div>
  </div>
</template>

<script>
  import a17CheckboxAccordion from '@/components/CheckboxAccordion.vue'

  export default {
    name: 'A17DamFilterDropdown',
    components: {
      'a17-checkboxaccordion': a17CheckboxAccordion
    },
    props: {
      label: {
        type: String,
        required: true
      },
      hasSearch: {
        type: Boolean,
        default: true
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
        isOpen: false,
        searchValue: null
      }
    },
    computed: {
      uid() {
        return this.label.replace(' ', '-').toLowerCase()
      },
      placeholder() {
        return 'Search ' + this.label.toLowerCase()
      }
    },
    watch: {},
    methods: {
      handleClick() {
        this.isOpen = !this.isOpen
      },
      onSearchInput() {}
    },
    mounted() {}
  }
</script>

<style lang="scss">
  .dam-filters__dropdown {
    .accordion:last-child {
      border-bottom: none;
    }

    .accordion__trigger {
      height: auto;

      @include breakpoint('medium+') {
        padding: rem-calc(12) rem-calc(44) rem-calc(12) rem-calc(20);
      }
    }
  }
</style>

<style lang="scss" scoped>
  .dam-filters__wrap {
    position: relative;
  }

  .dam-filters__toggle {
    display: flex;
    flex-flow: row;
    align-items: center;
    border: none;
    background: $color__border;

    .icon {
      margin-left: rem-calc(10);
    }

    &--open .icon {
      transform: rotate(180deg);
    }

    &--open + .dam-filters__dropdown {
      visibility: visible;
      opacity: 1;
    }
  }

  .dam-filters__dropdown {
    @include breakpoint('medium+') {
      background: $color__background;
      border-radius: 2px;
      position: absolute;
      border: 1px solid $color__border--light;
      box-shadow: 0px 1px 3.5px 0px rgba(0, 0, 0, 0.3);
      width: rem-calc(320);
      z-index: 20;
      margin-top: rem-calc(8);
      visibility: hidden;
      opacity: 0;
      transition: all 0.25s linear;
    }
  }

  .dam-filters__dropdown-search {
    @include breakpoint('medium+') {
      padding: rem-calc(16);
      border-bottom: 1px solid $color__border--light;
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

  .dam-filters__dropdown-footer {
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
