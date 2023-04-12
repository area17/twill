<template>
  <div class="language"
       :class="languageClass"
       v-if="languages.length > 1">
    <span class="language__label f--small" v-if="!inModal">{{ $trans('lang-switcher.edit-in') }}</span>
    <span class="language__toolbar">
      <button type="button" class="language__button" :key="language.value" v-for="language in languages"
              :class="{ 'selected': language.value === localeValue.value, 'published': language.published, 'no-state': allPublished }"
              @click="onClick(language.value)">{{ language.shortlabel }}</button>
    </span>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex'

  import LocaleMixin from '@/mixins/locale'
  import { LANGUAGE } from '@/store/mutations'

  export default {
    name: 'A17Langswitcher',
    mixins: [LocaleMixin],
    props: {
      inModal: {
        type: Boolean,
        default: false
      },
      toggleOnClick: {
        type: Boolean,
        default: false
      },
      allPublished: {
        type: Boolean,
        default: false
      }
    },
    computed: {
      languageClass () {
        return {
          'language--in-modal': this.inModal
        }
      },
      localeValue () {
        return this.$store.state.language.active
      },
      ...mapGetters([
        'publishedLanguages'
      ])
    },
    methods: {
      onClick: function (newValue) {
        this.$store.commit(LANGUAGE.UPDATE_LANG, newValue)
      }
    }
  }
</script>

<style lang="scss" scoped>

  $toolbar__height: 35px;
  $border__radius: 2px;

  .language {
    display: flex;
    align-items: center;
    justify-content: space-between;
    color: $color__f--text;
  }

  .language__label {
    line-height: $toolbar__height;
    height: $toolbar__height;
    margin-right: 10px;
    display: none;
    overflow: hidden;
    white-space: nowrap;

    @include breakpoint('medium+') {
      display: inline-block;
    }
  }

  .language__toolbar {
    display: inline-block;
    height: $toolbar__height + 2px;
    max-width: 480px;
    white-space: nowrap;
    overflow: hidden;
    border: 1px solid #d9d9d9;
    border-radius: 2px;

    &:hover {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }

    &::-webkit-scrollbar {
      width: 1px;
      height: 2px;
    }

    &::-webkit-scrollbar-button {
      width: 0px;
      height: 0px;
    }
    &::-webkit-scrollbar-thumb {
      width: 2px;
      background: rgba(115, 115, 115, 0.4);
      border: 0px none transparent;
      border-radius: 4px;
    }
    &::-webkit-scrollbar-thumb:hover {
      background: rgba(115, 115, 115, 0.6);
    }
    &::-webkit-scrollbar-thumb:active {
      background: rgba(0, 0, 0, 0.2);
    }
    &::-webkit-scrollbar-track {
      background: transparent;
      border: 0px none transparent;
      border-radius: 4px;
    }
    &::-webkit-scrollbar-track:hover {
      background: rgba(115, 115, 115, 0.2);
    }
    &::-webkit-scrollbar-track:active {
      background: rgba(51, 51, 51, 0);
    }
    &::-webkit-scrollbar-corner {
      background: transparent;
    }

    .language--in-modal & {
      height: $toolbar__height;
      max-width: 100%;
    }

    @include breakpoint('medium') {
      max-width: 320px;
    }

    @include breakpoint('small-') {
      max-width: 100%;
    }
  }

  .language__button {
    text-transform: uppercase;
    display: inline-block;
    height: $toolbar__height;
    line-height: $toolbar__height;
    border: 0 none;
    border-radius: 0;
    border-left: 1px solid $color__light;
    outline: 0;
    -webkit-appearance: none;
    cursor: pointer;
    @include font-tiny-btn;
    padding: 0 18px 0 30px;
    position: relative;
    color: $color__f--text;
    background: $color__background;
    white-space: nowrap;
    transition: background-color .25s linear, border-color .25s linear;
    margin-left: 0;
    margin-right: 0;

    .language--in-modal & {
      height: $toolbar__height - 2px;
      line-height: $toolbar__height - 2px;
    }

    &:hover,
    &:focus {
      color: $color__text;
    }

    &::after {
      content: '';
      position: absolute;
      border-radius: 50%;
      height: 7px;
      width: 7px;
      background-color: $color__icons;
      left: 15px;
      top: 50%;
      margin-top: -4px;
    }
  }

  .language__button:first-child {
    // border-top-left-radius: $border__radius;
    // border-bottom-left-radius: $border__radius;
    // border-left:1px solid $color__fborder;
    border-left: 0 none;
  }

  // .language__button:last-child {
  //   border-top-right-radius: $border__radius;
  //   border-bottom-right-radius: $border__radius;
  //   border-left:0 none;
  //   border-right:1px solid $color__fborder;
  // }

  .language__item.selected:last-child,
  .language__button.selected {
    background: $color__border;
    color: $color__text;
    border-color: $color__border;

    + .language__button {
      border-left-color: $color__border;
    }
  }

  .language__button.published::after {
    background-color: $color__green;
  }

  .language__button.no-state {
    padding: 0 18px 0 18px;

    &::after {
      content: none;
    }
  }
</style>
