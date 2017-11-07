<template>
  <form class="filter" :class="{ 'filter--opened' : openedFilter, 'filter--single' : !withNavigation }" @submit.prevent="submitFilter" ref="form">
    <div class="filter__inner">
      <div class="filter__navigation"><slot name="navigation"></slot></div>

      <div class="filter__search">
        <input type="search" class="form__input form__input--small" name="query" value="" :placeholder="placeholder" />
        <a17-button class="filter__toggle" variant="ghost" @click="toggleFilter" v-if="withHiddenFilters" :aria-expanded="openedFilter ?  'true' : 'false'" >Filter <span v-svg symbol="dropdown_module"></span></a17-button>
        <slot name="additional-actions"></slot>
      </div>
    </div>
    <div class="filter__more" v-if="withHiddenFilters" :aria-hidden="!openedFilter ?  true : null">
      <div class="filter__moreInner">
        <slot name="hidden-filters"></slot>
        <a17-button variant="ghost" type="submit">Apply</a17-button>
      </div>
    </div>
  </form>
</template>

<script>
  import FormDataAsObj from '@/utils/formDataAsObj.js'

  export default {
    name: 'A17Filter',
    props: {
      placeholder: {
        type: String,
        default: 'Search'
      }
    },
    data: function () {
      return {
        openedFilter: false,
        withHiddenFilters: true,
        withNavigation: true
      }
    },
    methods: {
      toggleFilter: function () {
        this.openedFilter = !this.openedFilter
      },
      submitFilter: function () {
        let formData = FormDataAsObj(this.$refs.form)
        this.$emit('submit', formData)
      }
    },
    beforeMount: function () {
      if (!this.$slots.navigation) this.withNavigation = false
      if (!this.$slots['hidden-filters']) this.withHiddenFilters = false
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .filter {
  }

  .filter__inner {
    display:flex;
  }

  .filter__navigation {
    flex-grow:1;
  }

  .filter__search {
    padding:20px 0;
    white-space: nowrap;

    input {
      display:inline-block;
      width:auto;
      min-width:300px;
      margin-right:15px;
    }

    .icon {
      position:relative;
      top:-2px;
      margin-left:9px;
    }

    div {
      display:inline-block;

      input, button {
        margin-left:15px;
      }
    }
  }

  .filter__more {
    max-height:0;
    height:auto;
    overflow:hidden;
    visibility: hidden;
    transition: max-height 0.3s linear, visibility 0s 0.3s;
  }

  .filter__moreInner {
    padding:20px 0;
    border-top:1px solid $color__border;

    .input {
      margin-top:0;
      margin-bottom:20px;
      margin-right: 20px;
    }
  }

  @include breakpoint('small+') {
    .filter__moreInner {
      display:flex;

      .input {
        margin-bottom:0;
      }

      > div {
        flex-grow:1;
        display:flex;

        > * {
          flex-grow: 1;
          flex-basis: 0;
        }
      }
    }
  }

  .filter__toggle {
    position:relative;
    padding-right:  20px + 20px !important;

    .icon {
      transition: all .2s linear;
      transform:rotate(0deg);
      position: absolute;
      right: 20px;
      top: 50%;
      margin-top: -3px;
    }
  }

  /* Opened filters */
  .filter--opened {
    .filter__more {
      visibility: visible;
      transition: max-height 0.3s linear;
      max-height:250px;
      overflow:visible;
    }

    .filter__toggle .icon {
      transform:rotate(180deg);
    }
  }

  .filter--single {
    .filter__navigation {
      display:none;
    }
  }
</style>
