<template>
  <div class="input" :class="textfieldClasses" :hidden="!this.isCurrentLocale ?  true : null">
    <label class="input__label" :for="name" v-if="label">{{ label }} <span class="input__lang" v-if="hasLocale" @click="onClickLocale" data-tooltip-title="Switch language" v-tooltip>{{ displayedLocale }}</span> <span class="input__note f--small" v-if="note">{{ note }}</span></label>
    <slot></slot>
  </div>
</template>

<script>
  import InputMixin from '@/mixins/input'
  import InputframeMixin from '@/mixins/inputFrame'
  import LocaleMixin from '@/mixins/locale'

  export default {
    name: 'A17Textfield',
    mixins: [InputMixin, InputframeMixin, LocaleMixin],
    computed: {
      textfieldClasses: function () {
        return {
          'input--error': this.error,
          'input--small': this.size === 'small',
          'input--hidden': !this.isCurrentLocale
        }
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .input {
    margin-top:35px;
  }

  .input__label {
    display:block;
    color:$color__text;
    margin-bottom:10px;
    position:relative;
  }

  .input__note {
    color:$color__text--light;
    right:0;
    top:1px;
    position:absolute;
  }

  .input__lang {
    border-radius:2px;
    display:inline-block;
    height:15px;
    line-height:15px;
    font-size:10px;
    color:$color__background;
    text-transform:uppercase;
    background:$color__icons;
    padding:0 5px;
    position:relative;
    top:-2px;
    margin-left:5px;
    cursor:pointer;
    user-select: none;
    letter-spacing:0;

    &:hover {
      background:$color__f--text;
    }
  }

  .input--hidden {
    display:none;
  }

  .input--error {
    label {
      color:$color__error;
    }

    .select__input,
    .input__field {
      border-color:$color__error;

      &:hover,
      &:focus {
        border-color:$color__error;
      }
    }
  }

  /* small variant */

  .input--small {
    margin-top:16px;

    .input__label {
      margin-bottom:9px;
      @include font-small;
    }
  }
</style>
