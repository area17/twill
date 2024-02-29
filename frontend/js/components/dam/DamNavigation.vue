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
          :aria-label="
            isOpen
              ? $trans('dam.close-nav', 'Close navigation')
              : $trans('dam.expand-nav', 'Expand navigation')
          "
          class="damNav__toggle"
          @click="togglePanel"
          ><span
            aria-hidden="true"
            v-svg
            :symbol="isOpen ? 'pagination_left' : 'pagination_right'"
          ></span
        ></a17-button>
        <a17-button
          :aria-label="$trans('dam.close-nav', 'Close navigation')"
          class="damNav__close"
          @click="isOpen = false"
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
            :placeholder="searchPlaceholder"
            @input="onSearchInput"
          />
          <a17-button
            variant="icon"
            :aria-label="$trans('dam.submit-search', 'Submit search')"
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
              <li
                v-for="(item, i) in section.items"
                :key="i"
                class="damNav__listItem"
                :class="{
                  'damNav__listItem--open': expandedSections.includes(
                    `section_${index}_${i}`
                  )
                }"
              >
                <component
                  :is="item.url ? 'a' : 'button'"
                  :href="item.url"
                  :class="[
                    'damNav__link',
                    {
                      'damNav__link--active': item.active
                    }
                  ]"
                  :id="`navItem_${index}_${i}`"
                  @click="
                    item.items && item.items.length
                      ? toggleSection(`section_${index}_${i}`)
                      : null
                  "
                  :aria-expanded="
                    item.items && item.items.length
                      ? expandedSections.includes(`section_${index}_${i}`)
                        ? 'true'
                        : 'false'
                      : null
                  "
                >
                  <span
                    v-if="expandedSections.includes(`section_${index}_${i}`)"
                    v-svg
                    symbol="dropdown_default"
                    aria-hidden="true"
                    class="damNav__icon"
                  >
                  </span>
                  <span
                    v-else-if="item.icon || section.icon"
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
                <ul
                  v-if="item.items && item.items.length > 0"
                  :aria-labelledby="`navItem_${index}_${i}`"
                >
                  <li
                    v-for="(childItem, childIndex) in item.items"
                    :key="childIndex"
                  >
                    <component
                      :is="childItem.url ? 'a' : 'button'"
                      :href="childItem.url"
                      :class="[
                        'damNav__link',
                        {
                          'damNav__link--active': childItem.active
                        }
                      ]"
                    >
                      <span v-svg symbol="nested" aria-hidden="true"></span>
                      <span class="damNav__item">
                        <span v-if="childItem.text" class="f--regular">{{
                          childItem.text
                        }}</span>
                        <span v-if="childItem.count" class="f--small">{{
                          formatNumber(childItem.count)
                        }}</span>
                      </span>
                    </component>
                  </li>
                </ul>
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
      searchPlaceholder: {
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
        expandedSections: [],
        isOpen: true,
        isScrolledToBottom: false,
        searchValue: this.initialSearchValue
      }
    },
    computed: {},
    watch: {
      isOpen(newVal) {
        sessionStorage.setItem('sideNavOpen', JSON.stringify(newVal))
      }
    },
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
      },
      toggleSection(item) {
        const index = this.expandedSections.indexOf(item)

        if (index !== -1) {
          this.expandedSections.splice(index, 1)
        } else {
          this.expandedSections.push(item)
        }
      }
    },
    mounted() {
      const sideNavOpen = sessionStorage.getItem('sideNavOpen')

      if (sideNavOpen === null) {
        sessionStorage.setItem('sideNavOpen', 'true')
        this.isOpen = true
      } else {
        this.isOpen = JSON.parse(sideNavOpen)
      }

      // TODO: Move this class to DAM template
      document.body.classList.add('body--hasSideNav')
    }
  }
</script>

<style lang="scss">
  body.body--hasSideNav {
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
    opacity: 0;
    visibility: hidden;
    transition: all 300ms ease;

    @include breakpoint('medium+') {
      transition: background-color 300ms ease;
      opacity: 1;
      visibility: visible;
    }
  }

  .damNav__wrapper--open {
    opacity: 1;
    visibility: visible;

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
      position: relative;
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

    @include breakpoint('xlarge') {
      position: relative;
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

    .damNav__listItem {
      border-radius: 2px;
      overflow: hidden;

      ul {
        height: 0;
        margin-top: 0;
      }

      ul li:first-child {
        margin-top: rem-calc(8);
      }
    }

    .damNav__listItem--open {
      background: $color__black--90;

      > .damNav__link .icon,
      > .damNav__link .f--regular {
        color: $color__white;
      }

      ul {
        height: auto;
      }
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
    background: transparent;
    border: none;
    color: $color__grey--54;
    cursor: pointer;

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

    .icon--nested {
      margin-right: rem-calc(8);
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
    pointer-events: none;
  }

  .damNav__item {
    width: 100%;
    display: flex;
    flex-flow: row;
    justify-content: space-between;
    pointer-events: none;

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
