<template>
  <div class="input" :class="textfieldClasses" v-show="isCurrentLocale" :hidden="!isCurrentLocale ? true : null">
    <label class="input__label" :for="labelFor || name" v-if="label">
      {{ label }}<span class="input__required" v-if="required">*</span>
      <span class="input__lang" v-if="hasLocale && languages.length > 1" @click="onClickLocale" :data-tooltip-title="$trans('fields.generic.switch-language')" v-tooltip>{{ displayedLocale }}</span>
      <span class="input__note f--small" v-if="note">{{ note }}</span>
    </label>
    <a href="#" v-if="addNew" @click.prevent="openAddModal" class="input__add"><span v-svg symbol="add"></span> <span class="f--link-underlined--o">Add New</span></a>
    <slot></slot>
    <span v-if="error && errorMessage" class="input__errorMessage f--small" v-html="errorMessage"></span>
    <span v-if="otherLocalesError" class="input__errorMessage f--small">{{ errorMessageLocales }}</span>
  </div>
</template>

<script>
  import InputMixin from '@/mixins/input'
  import InputframeMixin from '@/mixins/inputFrame'
  import LocaleMixin from '@/mixins/locale'

  export default {
    name: 'A17InputFrame',
    mixins: [InputMixin, InputframeMixin, LocaleMixin],
    props: {
      addNew: {
        type: String,
        default: ''
      }
    },
    computed: {
      textfieldClasses: function () {
        const classes = [
          this.size === 'small' ? 'input--small' : '',
          this.error ? 'input--error' : '',
          'input-wrapper-' + (this.name || this.labelFor)
        ]

        if (this.variant) {
          this.variant.split(' ').forEach((val) => {
            classes.push(`input--${val}`)
          })
        }

        return classes
      }
    },
    methods: {
      openAddModal: function () {
        if (this.$parent.$refs.addModal) this.$parent.$refs.addModal.open()
      }
    }
  }
</script>

<style lang="scss" scoped>

  .input {
    margin-top:35px;
    position: relative;
  }

  .input:empty {
    display:none;
  }

  .input__add {
    position:absolute;
    top:0;
    right:0;
    text-decoration:none;
    color:$color__link;
  }

  .input__label {
    display:block;
    color:$color__text;
    margin-bottom:10px;
    word-wrap:break-word;
    position:relative;
  }

  .input__note {
    color:$color__text--light;
    display:block;

    @include breakpoint('small+') {
      display:inline;
      right:0;
      top:1px;
      position:absolute;
    }
  }

  .input__required {
    color:$color__icons;
    padding-left:5px;
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

  /* Input inline */
  .input__inliner {
    > .input {
      display:inline-block;
      margin-top:0;
      margin-right: 20px;

      .singleCheckbox {
        padding:7px 0 8px 0;
      }
    }
  }

  /* Variant input in table */
  .input--intable {
    margin-top:0;

    @include breakpoint('large+') {
      display: flex;
      align-items: center;

      .input__label {
        flex-grow: 1;
        margin-bottom:0;
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

  /* Error variant */
  .input--error {
    > label {
      color:$color__error;

      .input__lang {
        background-color:$color__error;
      }
    }

    .form__field,
    .select__input,
    .input__field,
    .v-select .dropdown-toggle {
      border-color:$color__error;

      &.s--focus,
      &:hover,
      &:focus {
        border-color:$color__error;
      }
    }
  }

  .input__errorMessage {
    color:$color__error;
    margin-top:10px;
    display:block;
  }
</style>
