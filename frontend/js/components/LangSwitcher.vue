<template>
  <div class="language" v-if="languages.length > 1">
    <span class="language__label f--small">Edit in</span>
    <span class="language__toolbar">
      <button type="button" class="language__button" v-for="language in languages" :class="{ 'selected': language.value === localeValue.value, 'published': language.published }" @click="onClick(language.value)" >{{ language.shortlabel }}</button>
    </span>
  </div>
</template>

<script>
  import LocaleMixin from '@/mixins/locale'

  export default {
    name: 'A17Langswitcher',
    mixins: [LocaleMixin],
    computed: {
      localeValue () {
        return this.$store.state.language.active
      }
    },
    methods: {
      onClick: function (newValue) {
        this.$store.commit('updateLanguage', newValue)
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  $toolbar__height:35px;
  $border__radius:2px;

  .language {
    color: $color__f--text;
  }

  .language__label {
    line-height:$toolbar__height;
    height:$toolbar__height;
    margin-right:10px;
    display:none;
    overflow:hidden;

    @include breakpoint('medium+') {
      display:inline-block;
    }
  }

  .language__toolbar {
    display:inline-block;
    height:$toolbar__height;
    overflow:hidden;
  }

  .language__toolbar {
    border:1px solid $color__fborder;
    border-radius:$border__radius;
  }

  .language__button {
    text-transform: uppercase;
    display:inline-block;
    height:$toolbar__height - 2px;
    line-height:$toolbar__height - 2px;
    border:0 none;
    border-radius:0;
    // border:1px solid $color__fborder;
    border-left:1px solid $color__light;
    // border-right:0 none;
    outline:0;
    -webkit-appearance: none;
    cursor: pointer;
    font-size:0.75em;
    padding:0 18px 0 30px;
    position:relative;
    color:$color__f--text;
    background: $color__background;
    white-space: nowrap;
    transition: background-color .25s linear, border-color .25s linear;
    margin-left:0;
    margin-right:0;

    &:hover,
    &:focus {
      color:$color__text;
    }

    &::after {
      content:'';
      position:absolute;
      border-radius:50%;
      height:7px;
      width:7px;
      background-color:$color__icons;
      left:15px;
      top:50%;
      margin-top:-4px;
    }
  }

  .language__button:first-child {
    // border-top-left-radius: $border__radius;
    // border-bottom-left-radius: $border__radius;
    // border-left:1px solid $color__fborder;
    border-left:0 none;
  }

  .language__button:last-child {
    // border-top-right-radius: $border__radius;
    // border-bottom-right-radius: $border__radius;
    // border-left:0 none;
    // border-right:1px solid $color__fborder;
  }

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
    background-color:$color__green;
  }

</style>
