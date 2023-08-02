<template>
  <div class="modalValidation">
    <a17-inputframe>
      <template v-if="mode === 'create'">
        <a17-button type="submit" name="create" variant="validate" :disabled="isDisabled">{{ $trans('modal.create.button', 'Create') }}</a17-button>
        <a17-button type="submit" name="create-another" v-on:click.native="$event.currentTarget.focus()" v-if="!isDisabled" variant="aslink-grey"><span>{{ $trans('modal.create.create-another', 'Create and add another') }}</span></a17-button>
      </template>
      <a17-button type="submit" name="update" v-else-if="mode === 'update'" variant="validate" :disabled="isDisabled">{{ $trans('modal.update.button', 'Update') }}</a17-button>
      <a17-button type="submit" name="done" v-else variant="validate" :disabled="isDisabled">{{ $trans('modal.done.button', 'Done') }}</a17-button>
    </a17-inputframe>
    <label v-if="activePublishState" :for="publishedName" class="switcher__button" :class="switcherClasses">
      <span v-if="isChecked" class="switcher__label">{{ textEnabled }}</span>
      <span v-if="!isChecked" class="switcher__label">{{ textDisabled }}</span>

      <input type="checkbox" :disabled="disabled" v-model="published" :name="publishedName" :id="publishedName" :value="1"/>
      <span class="switcher__switcher"></span>
    </label>
  </div>
</template>

<script>
  import { FORM } from '@/store/mutations'

  export default {
    name: 'A17ModalValidationButtons',
    props: {
      publishedName: {
        type: String,
        required: false
      },
      disabled: {
        type: Boolean,
        default: false
      },
      activePublishState: {
        type: Boolean,
        default: false
      },
      isPublish: {
        type: Boolean,
        default: false
      },
      isDisable: {
        type: Boolean,
        default: false
      },
      mode: {
        type: String, // create / update
        default: 'create'
      },
      textEnabled: {
        type: String,
        default: 'Live'
      },
      textDisabled: {
        type: String,
        default: 'Draft'
      }
    },
    data: function () {
      return {
        fields: false,
        isDisabled: this.isDisable,
        published: this.isPublish
      }
    },
    watch: {
      published: function (value) {
        this.$store.commit(FORM.UPDATE_FORM_FIELD, {
          name: 'published',
          value
        })
      }
    },
    computed: {
      switcherClasses: function () {
        return [
          this.isChecked ? 'switcher--active' : ''
        ]
      },
      isChecked: function () {
        return this.published
      },
      checkedValue: {
        get: function () {
          return this.published
        },
        set: function (value) {
          this.published = value
        }
      }
    },
    methods: {
      addListeners () {
        this.$nextTick(() => {
          this.fields.forEach((field) => {
            field.removeEventListener('input', this.disable)
          })
          this.fields = [...this.$parent.$el.querySelectorAll('input, textarea, select')]
          this.fields.forEach((field) => {
            field.addEventListener('input', () => {
              this.disable()
            })
          })
        })
      },
      disable: function () {
        if (!this.fields) {
          this.isDisabled = true
          this.$emit('disable', true)
          return
        }

        const requiredFields = this.fields.filter((field) => {
          return field.getAttribute('required')
        })

        // There are no required fields, so buttons are enabled
        if (requiredFields.length === 0) {
          this.isDisabled = false
          this.$emit('disable', false)
          return
        }

        // If all required fields must have a value
        const filtered = requiredFields.filter(function (field) {
          return field.value.length > 0
        })

        if (filtered.length === requiredFields.length) {
          this.isDisabled = false
          this.$emit('disable', false)
          return
        }

        this.isDisabled = true
        this.$emit('disable', true)
      }
    },
    mounted: function () {
      const self = this

      this.fields = [...this.$parent.$el.querySelectorAll('input, textarea, select')]

      // check disable state on init
      self.disable()

      if (!this.fields.length) return

      this.addListeners()
    },
    beforeDestroy: function () {
      const self = this

      if (!this.fields.length) return

      this.fields.forEach(function (field) {
        field.removeEventListener('input', self.disable)
      })
    }
  }
</script>

<style lang="scss" scoped>

  .modalValidation {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 35px;
  }

  .switcher__button {
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
    min-width: 125px;
    height: 40px;
    line-height: 40px;
    padding: 0 15px 0 20px;
    border-radius: 20px;
    color: $color__text;
    background: $color__button_disabled-bg;

    cursor: pointer;
    transition: background-color .25s linear, color .25s linear;

    input {
      position: absolute;
      opacity: 0;
    }
  }

  .switcher__label {
    margin-right: 15px;
  }

  .switcher__switcher {
    display: inline-block;
    height: 12px;
    border-radius: 6px;
    width: 40px;
    background: $color__text--forms;
    box-shadow: inset 0 0 1px #000;
    position: relative;

    // Big rounded thing
    &::after,
    &::before {
      content: "";
      position: absolute;
      display: block;
      height: 18px;
      width: 18px;
      border-radius: 50%;
      left: 0;
      top: -3px;
      transform: translateX(0);
      transition: all .25s $bezier__bounce;
    }

    // Big rounded thing you want to click
    &::after {
      background: $color__background;
      box-shadow: 0 0 1px #666;
    }

    // Big rounded thing for hover / focus states only
    &::before {
      background: $color__background;
      box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.1);
      opacity: 0;
    }
  }

  .switcher--active {
    background: $color__lightGreen;
    color: $color__publish;

    .switcher__switcher {
      background: $color__publish;
      box-shadow: inset 0 0 1px rgba($color__black, 0.4);
    }

    .switcher__switcher::after,
    .switcher__switcher::before {
      transform: translateX(40px - 18px);
    }
  }

  /* Show something when hover / focus */
  .switcher__button {
    input:focus + .switcher__switcher::before {
      opacity: 1;
    }
  }

  .switcher__button:hover,
  .switcher__button:focus {
    .switcher__switcher::before {
      opacity: 1;
    }
  }
</style>

<style lang="scss">
  .modalValidation {
    .input {
      margin-top: 0;
    }
  }
</style>
