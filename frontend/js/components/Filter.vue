<template>
  <form class="filter" :class="{ 'filter--opened' : opened, 'filter--single' : !withNavigation }" @submit.prevent="submitFilter" ref="form">
    <div class="filter__inner">
      <div class="filter__navigation"><slot name="navigation"></slot></div>

      <div class="filter__search">
        <input type="search" class="form__input form__input--small" name="query" value="" :placeholder="placeholder" />
        <a17-button class="filter__toggle" variant="ghost" @click="toggleFilter" v-if="withHiddenFilters" :aria-expanded="opened ?  'true' : 'false'" >Filter <span v-svg symbol="dropdown_module"></span></a17-button>
        <slot name="additional-actions"></slot>
      </div>
    </div>
    <transition name="scale_filter" @before-enter="beforeAnimate" @after-enter="afterAnimate" @before-leave="beforeAnimate" @after-leave="afterAnimate">
      <div class="filter__more" v-if="withHiddenFilters" :aria-hidden="!opened ?  true : null" v-show="opened">
        <div class="filter__moreInner" >
          <slot name="hidden-filters"></slot>
          <a17-button variant="ghost" type="submit">Apply</a17-button>
        </div>
      </div>
    </transition>
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
        opened: false,
        withHiddenFilters: true,
        withNavigation: true
      }
    },
    methods: {
      beforeAnimate: function (el) {
        el.style.overflow = 'hidden'
      },
      afterAnimate: function (el) {
        el.style.overflow = 'visible'
      },
      toggleFilter: function () {
        this.opened = !this.opened
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

      /deep/ .input {
        margin-top:0;
      }

      /deep/ > div {
        flex-grow:1;
        display:flex;
      }

      /deep/ > div > * {
        margin-right:20px;
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
