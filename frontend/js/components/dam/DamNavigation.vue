<template>
  <div
    class="damNav__wrapper"
    :class="[{ 'damNav__wrapper--open': isOpen }]"
    @click.prevent="onClickOutside"
  >
    <div class="damNav" ref="nav">
      <div class="damNav__header">
        <h1>
          <component :is="appLink ? 'a' : 'span'" :href="appLink">
            {{ appName }}
            <span class="envlabel">
              {{ envLabel }}
            </span>
          </component>
        </h1>
        <a17-button
          aria-label="Expand/close"
          class="damNav__toggle"
          @click="togglePanel"
          ><span
            aria-hidden="true"
            v-svg
            :symbol="isOpen ? 'pagination_left' : 'pagination_right'"
          ></span
        ></a17-button>
        <a17-button aria-label="Close" class="damNav__close"
          ><span v-svg aria-hidden="true" symbol="close"></span
        ></a17-button>
      </div>
      <div
        class="damNav__body"
        :class="{ scrolled: isScrolledToBottom }"
        ref="navBody"
        @scroll="handleScroll"
      >
        <div class="damNav__search">
          <input
            type="search"
            class="form__input form__input--small"
            name="search"
            :value="searchValue"
            :placeholder="placeholder"
            @input="onSearchInput"
          />
          <a17-button variant="icon"
            ><span v-svg aria-hidden="true" symbol="search"></span
          ></a17-button>
        </div>
        <div v-for="(section, index) in JSON.parse(this.sections)" :key="index">
          <h2
            v-if="section.title && !section.hideTitle"
            class="f--tiny"
            :id="`navList__${index}`"
          >
            {{ section.title
            }}<template v-if="section.items && section.items.length > 0">
              ({{ section.items.length }})</template
            >
          </h2>
          <template v-if="section.items && section.items.length > 0">
            <ul :aria-labelledby="`navList__${index}`">
              <li v-for="(item, i) in section.items" :key="i">
                <component
                  :is="item.url ? 'a' : 'span'"
                  :href="item.url"
                  :class="[
                    {
                      damNav__link: item.url,
                      'damNav__link--active': item.active
                    }
                  ]"
                >
                  <span
                    v-if="item.icon || section.icon"
                    v-svg
                    :symbol="item.icon ? item.icon : section.icon"
                    aria-hidden="true"
                    class="damNav__icon"
                  ></span>
                  <span class="damNav__item">
                    <span v-if="item.text" class="f--regular">{{
                      item.text
                    }}</span>
                    <span v-if="item.count" class="f--small">{{
                      formatNumber(item.count)
                    }}</span>
                  </span>
                </component>
              </li>
            </ul>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import { getCurrentLocale } from '@/utils/locale'

  export default {
    name: 'A17DamNavigation',
    props: {
      appLink: {
        type: String,
        default: null
      },
      appName: {
        type: String,
        default: null
      },
      envLabel: {
        type: String,
        default: null
      },
      placeholder: {
        type: String,
        default() {
          return this.$trans('filter.search-placeholder', 'Search')
        }
      },
      sections: {
        type: String,
        default: null
      }
    },
    data() {
      return {
        isOpen: false,
        isScrolledToBottom: false,
        searchValue: this.initialSearchValue
      }
    },
    computed: {},
    methods: {
      formatNumber(number) {
        return number.toLocaleString(getCurrentLocale())
      },
      getCurrentMediaQuery() {
        if (typeof window === 'undefined') return ''
        return getComputedStyle(document.documentElement)
          .getPropertyValue('--breakpoint')
          .trim()
          .replace(/"/g, '')
      },
      handleScroll() {
        const navBody = this.$refs.navBody
        if (navBody.scrollHeight - navBody.scrollTop === navBody.clientHeight) {
          this.isScrolledToBottom = true
        } else {
          this.isScrolledToBottom = false
        }
      },
      onClickOutside(event) {
        const mq = this.getCurrentMediaQuery()

        if (
          (mq === 'medium' || mq === 'large') &&
          this.isOpen &&
          event.target !== this.$refs.nav &&
          !this.$refs.nav.contains(event.target)
        ) {
          this.isOpen = false
        }
      },
      onSearchInput(event) {
        this.searchValue = event.target.value
      },
      togglePanel(e) {
        this.isOpen = !this.isOpen
      }
    },
    mounted() {}
  }
</script>

<style lang="scss">
  body.has-sideNav {
    .a17 {
      @include breakpoint('medium+') {
        padding-left: rem-calc(74);
      }

      @include breakpoint('xlarge') {
        padding-left: 0;
      }
    }

    .icon svg {
      pointer-events: none;
    }
  }
</style>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .damNav__wrapper {
    @include breakpoint('medium+') {
      transition: background-color 300ms ease;
    }
  }

  .damNav__wrapper--open {
    @include breakpoint('medium+') {
      background: rgba($color__black, 0.6);
      position: fixed;
      inset: 0;
      z-index: $zindex__overlay;

      .damNav {
        width: rem-calc(312);
      }

      .damNav__header h1,
      .damNav__body {
        opacity: 1;
        visibility: visible;
      }

      .damNav__header {
        border-bottom: 1px solid $color__grey--85;
      }
    }

    @include breakpoint('xlarge') {
      background: none;
    }
  }

  .damNav {
    background: $color__black;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100vh;
    z-index: $zindex__overlay;
    display: flex;
    flex-flow: column;
    color: $color__grey--54;
    overflow: hidden;

    @include breakpoint('medium+') {
      width: rem-calc(76);
      transition: all 300ms ease;
    }
  }

  .damNav__header {
    padding: rem-calc(16) rem-calc(36) rem-calc(16) rem-calc(16);
    color: $color__white;
    border-bottom: 1px solid $color__grey--85;
    flex-shrink: 0;
    height: rem-calc(100);

    @include breakpoint('medium+') {
      padding: 0 rem-calc(76) 0 rem-calc(20);
      height: rem-calc(76);
      display: flex;
      align-items: center;
      width: rem-calc(312);
      border-color: transparent;
      transition: border-color 300ms ease;

      h1 {
        opacity: 0;
        visibility: hidden;
        transition: all 300ms ease;
      }
    }

    a {
      text-decoration: none;
    }

    .damNav__toggle {
      width: rem-calc(36);
      height: rem-calc(36);
      color: $color__grey--54;
      border: 1px solid $color__header--sep;
      line-height: normal;
      padding: 0;
      line-height: normal;
      position: absolute;
      right: rem-calc(20);
      display: none;
      align-items: center;
      justify-content: center;
      border-radius: rem-calc(2);

      &:hover {
        background: $color__header--sep;
      }

      @include breakpoint('medium+') {
        display: flex;
      }
    }

    .damNav__close {
      color: $color__grey--54;
      width: rem-calc(20);
      height: rem-calc(20);
      padding: 0;
      line-height: normal;
      position: absolute;
      top: rem-calc(16);
      right: rem-calc(16);

      @include breakpoint('medium+') {
        display: none;
      }
    }
  }

  .damNav__body {
    padding: rem-calc(20) rem-calc(16);
    flex-grow: 1;
    overflow-y: auto;
    display: flex;
    flex-flow: column;
    gap: rem-calc(40);
    -ms-overflow-style: none;
    scrollbar-width: none;

    @include breakpoint('medium+') {
      padding: rem-calc(20) rem-calc(20);
      width: rem-calc(312);
      opacity: 0;
      visibility: hidden;
      transition: all 300ms ease;
    }

    .f--tiny {
      text-transform: uppercase;
    }

    ul {
      display: flex;
      flex-flow: column;
      gap: rem-calc(8);

      &:not(:first-child) {
        margin-top: rem-calc(8);
      }
    }

    ul li {
      display: flex;
      flex-flow: row;
    }

    &::after {
      content: '';
      position: absolute;
      height: rem-calc(100);
      left: 0;
      bottom: 0;
      right: 0;
      background: linear-gradient(180deg, rgba(0, 0, 0, 0) 0%, #000 100%);
      transition: all 200ms ease;
    }

    &.scrolled::after {
      opacity: 0;
      visibility: hidden;
    }
  }

  .damNav__link {
    padding: rem-calc(4);
    display: flex;
    flex-flow: row;
    text-decoration: none;
    transition: color 100ms ease;
    width: 100%;
    border-radius: rem-calc(2);

    &:hover,
    &.damNav__link--active {
      background: $color__black--90;

      .f--regular,
      .damNav__icon {
        color: $color__white;
      }
    }

    &.damNav__link--active {
      background: $color__grey--85;
    }
  }

  .damNav__icon {
    flex-shrink: 0;
    margin-right: rem-calc(8);
    width: rem-calc(20);
    height: rem-calc(20);
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .damNav__item {
    width: 100%;
    display: flex;
    flex-flow: row;
    justify-content: space-between;

    .f--small {
      margin-left: rem-calc(8);
    }
  }

  .damNav__search {
    position: relative;
    margin-bottom: rem-calc(-20);

    @include breakpoint('medium+') {
      display: none;
    }
  }

  .damNav__search .form__input {
    padding-right: rem-calc(36);
  }

  .damNav__search .button {
    position: absolute;
    right: rem-calc(8);
    top: rem-calc(8);
    width: rem-calc(20);
    height: rem-calc(20);
    padding: 0;
    line-height: normal;
    border-radius: 0;
    border: none;
  }
</style>
