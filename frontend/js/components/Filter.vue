<template>
  <form class="filter" :class="{ 'filter--opened' : opened, 'filter--single' : !withNavigation, 'filter--withHiddenFilters' : withHiddenFilters }" @submit.prevent="submitFilter" ref="form">
    <div class="filter__inner">
      <div class="filter__navigation"><slot name="navigation"></slot></div>

      <div class="filter__search">
        <input type="search" class="form__input form__input--small" name="search" :value="searchValue" :placeholder="placeholder" @input="onSearchInput" />
        <a17-button class="filter__toggle" variant="ghost" @click="toggleFilter" v-if="withHiddenFilters" :aria-expanded="opened ?  'true' : 'false'" >Filter <span v-svg symbol="dropdown_module"></span></a17-button>
        <slot name="additional-actions"></slot>
      </div>
    </div>
    <transition :css='false' :duration="275" @before-enter="beforeEnter" @enter="enter" @after-enter="afterEnter" @before-leave="beforeLeave" @leave="leave">
      <div class="filter__more" v-show="opened" v-if="withHiddenFilters" :aria-hidden="!opened ? true : null">
        <div class="filter__moreInner" >
          <slot name="hidden-filters"></slot>
          <a17-button variant="ghost" type="submit">Apply</a17-button>
          <a17-button v-if="clearOption" variant="ghost" type="button" @click="clear">Clear</a17-button>
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
      initialSearchValue: {
        type: String,
        default: ''
      },
      placeholder: {
        type: String,
        default: 'Search'
      },
      closed: {
        type: Boolean,
        default: false
      },
      clearOption: {
        type: Boolean,
        default: false
      }
    },
    data: function () {
      return {
        openable: !this.closed,
        open: false,
        withHiddenFilters: true,
        withNavigation: true,
        searchValue: this.initialSearchValue
      }
    },
    computed: {
      opened: function () {
        return this.open && this.openable
      }
    },
    watch: {
      closed: function () {
        this.openable = !this.closed
      },
      initialSearchValue: function () {
        this.searchValue = this.initialSearchValue
      }
    },
    methods: {
      getMaxHeight: function () { // retrieve max height depending on the content height
        return Math.min(250, this.$el.querySelector('.filter__moreInner').clientHeight)
      },
      beforeEnter: function (el) {
        el.style.maxHeight = '0px'
        el.style.overflow = 'hidden'
      },
      enter: function (el, done) {
        el.style.maxHeight = this.getMaxHeight() + 'px'

        const timeOut = window.setTimeout(function () {
          done()
          window.clearTimeout(timeOut)
        }, 275 + 1)
      },
      afterEnter: function (el) {
        el.style.maxHeight = ''
        el.style.overflow = 'visible'
      },
      beforeLeave: function (el) {
        el.style.maxHeight = this.getMaxHeight() + 'px'
        el.style.overflow = 'hidden'
      },
      leave: function (el, done) {
        el.style.maxHeight = '0px'
      },
      toggleFilter: function () {
        this.openable = true
        this.open = !this.open
      },
      submitFilter: function () {
        let formData = FormDataAsObj(this.$refs.form)
        this.$emit('submit', formData)
      },
      onSearchInput: function (event) {
        this.searchValue = event.target.value
      },
      clear: function () {
        this.searchValue = ''
        this.$emit('clear')
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

  .filter__inner {
    display:flex;
    justify-content: space-between;
  }

  .filter__search {
    padding:20px 0;
    white-space: nowrap;

    input {
      display:inline-block;
      width:20vw;
      max-width:300px;
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

  /* variant when filter has hidden filters on small screens */
  @include breakpoint(xsmall) {
    .filter--withHiddenFilters {
      .filter__inner {
        display:block;
      }

      .filter__search {
        display:flex;

        input {
          flex-grow:1;
        }
      }
    }
  }

  .filter__more {
    max-height: 200px;
    transition: max-height 0.275s ease;
    overflow: hidden;
  }

  .filter__moreInner {
    padding:20px 0 0 0;
    border-top:1px solid $color__border;

    button {
      margin-right: 10px;
      margin-bottom:20px;
    }
  }

  @include breakpoint('small+') {
    .filter__moreInner {
      display:flex;
    }
  }

  .filter__toggle {
    position:relative;
    padding-right:  20px + 20px !important;
    margin-left:15px !important;

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

<style lang="scss">
  @import '~styles/setup/_mixins-colors-vars.scss';

  .filter {
    .filter__moreInner {
      .input {
        margin-top: 0;
        margin-bottom: 20px;
        margin-right: 20px;
      }
    }

    @include breakpoint('small+') {
      .filter__moreInner {
        .input {
          margin-top: 0;
        }

        > div {
          display: flex;
        }

        > div > * {
          margin-right: 20px;
        }
      }
    }
  }
</style>
