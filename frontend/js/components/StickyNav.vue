<template>
  <div class="stickyNav">
    <div class="container">
      <div class="stickyNav__nav">
        <div class="stickyNav__links" v-if="navItems.length > 1">
          <a href="#" v-for="(item, index) in navItems" :key="item.fieldset" @click.prevent="scrollToFieldset(index)" :class="{ 's--on' : item.active }">{{ item.label }}</a>
        </div>
        <slot name="title"></slot>
      </div>

      <div class="stickyNav__actions">
        <slot name="actions"></slot>
      </div>
    </div>
  </div>
</template>

<script>
  import debounce from 'lodash/debounce'
  import Vue from 'vue'

  import scrollToY from '@/utils/scrollToY.js'

  export default {
    name: 'A17StickyNav',
    props: {
      items: {
        type: Array,
        default: function () {
          return []
        }
      }
    },
    data: function () {
      return {
        lastScrollPos: 0,
        topOffset: 70,
        ticking: false,
        navItems: this.items,
        clickedFieldset: -1,
        fieldset: []
      }
    },
    methods: {
      getFieldsetPosition: function () {
        const self = this

        this.lastScrollPos = window.pageYOffset

        this.navItems.forEach(function (item, index) {
          const fieldset = self.fieldset[index]
          const pos = fieldset ? (fieldset.getBoundingClientRect().top + self.lastScrollPos) : 0

          Vue.set(item, 'position', pos)
        })
      },
      setActiveItems: function () {
        const self = this
        let itemToActivate = 0

        // desactivate all fieldset
        this.navItems.forEach(function (item, index) {
          const isActive = ((item.position - self.topOffset) < self.lastScrollPos)

          Vue.set(item, 'active', false)
          if (isActive && index > 0) itemToActivate = index
        })

        // no active, let fallback on the first one or the last one the user clicked
        if (this.clickedFieldset >= 0){
          Vue.set(self.navItems[self.clickedFieldset], 'active', true)
        }
        else if (self.navItems[itemToActivate] !== undefined) {
          Vue.set(self.navItems[itemToActivate], 'active', true)
        }
      },
      refresh: function () {
        const self = this

        self.clickedFieldset = -1
        this.getFieldsetPosition()
        this.setActiveItems()

        self.ticking = false
      },
      _resize: debounce(function () {
        this.lastScrollPos = window.pageYOffset
        this.refresh()
      }, 200),
      _scroll: function () {
        const self = this

        this.lastScrollPos = window.pageYOffset

        if (!self.ticking) {
          window.requestAnimationFrame(function () {
            self.refresh()
            self.ticking = false
          })
        }

        self.ticking = true
      },
      scrollToFieldset: function (index) {
        const self = this
        const ypos = this.navItems[index].position - this.topOffset + 1

        this.dispose()
        this.clickedFieldset = index
        this.getFieldsetPosition()
        this.setActiveItems()

        scrollToY({
          offset: ypos,
          easing: 'easeOut',
          onComplete: function () {
            self.init()
          }
        })
      },
      init: function () {
        window.addEventListener('scroll', this._scroll)
        window.addEventListener('resize', this._resize)
      },
      dispose: function () {
        window.removeEventListener('scroll', this._scroll)
        window.removeEventListener('resize', this._resize)
      }
    },
    mounted: function () {
      const self = this

      this.navItems.forEach(function (item, index) {
        const target = document.querySelector('#' + item.fieldset)

        if (target) self.fieldset.push(target)
        else self.fieldset.push(null)
      })

      this.refresh()
      this.init()
    },
    beforeDestroy: function () {
      this.dispose()
    }
  }
</script>

<style lang="scss" scoped>

  .stickyNav {
    background-color:rgba($color__border--light, 0.95);
    border-bottom:1px solid rgba($color__black, 0.05);
    background-clip: padding-box;
  }

  @include breakpoint('medium+') {
    .stickyNav {
      height:90px;
      z-index:$zindex__stickyNav;
      overflow:hidden;

      &.sticky__fixed,
      &.sticky__fixedTop,
      &.sticky__fixedBottom {
        height:60px;

        .container {
          padding-top:14px;
        }

        .stickyNav__links {
          opacity:1;
          visibility: visible;
          transition: opacity 0.25s;
        }

        .titleEditor {
          opacity:0;
          visibility: hidden;
          transition: opacity 0.25s ease, visibility 0s 0.25s;
        }

        .titleEditor:first-child {
          opacity:1;
          visibility: visible;
          transition: opacity 0.25s;
        }
      }
    }
  }

  .titleEditor {
    opacity:1;
    visibility: visible;
    transition: opacity 0.25s;
  }

  .stickyNav__links {
    opacity:0;
    visibility: hidden;
    transition: opacity 0.25s ease, visibility 0s 0.25s;
    display:flex;

    a {
      display:block;
      height:35px;
      line-height:35px;
      border-radius:17px;
      padding:0 17px;
      text-decoration:none;
      color: $color__text--light;
      background-color:rgba(white, 0);
      transition: background-color 0.25s linear;

      &:hover {
        color:$color__text;
      }

      &.s--on {
        background-color:white;
        color:$color__text;
      }
    }
  }

  .stickyNav__nav {
    position:relative;

    .stickyNav__links {
      position:absolute;
    }
  }

  .stickyNav__actions > div {
    display:flex;

    .button {
      margin-left:20px;

      @include breakpoint('small-') {
        margin-left: 0;
        margin-top: 20px;
      }
    }

    @include breakpoint('small-') {
      flex-direction: column;
    }
  }

  .stickyNav .container {
    display: block;
    padding-top:26px;
    padding-bottom:26px;

    @include breakpoint('medium+') {
      display: flex;
    }
  }

  .stickyNav__nav {
    @include breakpoint('medium+') {
      flex-grow: 1;
    }
  }
</style>
