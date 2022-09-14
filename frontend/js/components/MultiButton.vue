<template>
  <div class="multibutton">
    <a17-dropdown ref="submitDown" position="bottom-right" width="full" :offset="0">
      <a17-button v-if="isDisabled(options[0])" type="button" variant="validate" :disabled="true">{{ options[0].text }}</a17-button>
      <a17-button v-else :type="type" @click="buttonClicked(options[0].name)" :name="options[0].name" variant="validate">{{ options[0].text }}</a17-button>
      <template v-if="otherOptions.length">
      <button class="multibutton__trigger" type="button" @click="$refs.submitDown.toggle()" v-if="hasValidOptions"><span v-svg symbol="dropdown_module"></span></button>
        <div slot="dropdown__content">
          <ul>
            <li v-for="option in otherOptions" :key="option.name">
              <button v-if="isDisabled(option)" type="button" disabled>{{ option.text }}</button>
              <button v-else @click="buttonClicked(option.name)" :type="type" :name="option.name">{{ option.text }}</button>
            </li>
          </ul>
        </div>
      </template>
    </a17-dropdown>
  </div>
</template>

<script>
  import { NOTIFICATION } from '@/store/mutations'

  export default {
    name: 'A17Multibutton',
    props: {
      type: {
        default: 'button'
      },
      message: {
        type: String,
        default: ''
      },
      options: {
        default: function () { return [] }
      }
    },
    data: function () {
      return {}
    },
    computed: {
      otherOptions: function () {
        if (this.options.length) return this.options.slice(1)
        else return []
      },
      hasValidOptions: function () {
        const allValidOptions = this.options.filter(function (opt) {
          return !opt.hasOwnProperty('disabled') || opt.disabled === false
        })

        const hasValidOptions = Boolean(allValidOptions.length > 0)

        if (!hasValidOptions && this.message) {
          this.$store.commit(NOTIFICATION.SET_NOTIF, {
            message: this.message,
            variant: 'success'
          })
        }

        return hasValidOptions
      }
    },
    methods: {
      isDisabled: function (btn) {
        if (btn.hasOwnProperty('disabled')) {
          return btn.disabled === true
        } else {
          return false
        }
      },
      buttonClicked: function (val) {
        this.$emit('button-clicked', val)
      }
    }
  }
</script>

<style lang="scss" scoped>

  $height_btn: 40px;

  .multibutton {
    height:$height_btn;
    position:relative;
    display:block;

    .dropdown {
      display:flex;

      > button:first-child {
        display:block;
        flex-grow: 1;
      }
    }

    .dropdown__content {
      max-width:100%;
      width:100%;
    }
  }

  .multibutton__trigger {
    @include btn-reset;
    height:$height_btn;
    line-height:$height_btn;
    text-align:center;
    border-top-right-radius:2px;
    border-bottom-right-radius:2px;
    border-top-left-radius:0;
    border-bottom-left-radius:0;
    background:$color__ok;
    color: $color__background;
    margin-left: -2px;
    border-left:1px solid $color__ok--hover;
    padding:0 10px;
    transition: color .2s linear, border-color .2s linear, background-color .2s linear;

    &:focus,
    &:hover {
      background:$color__ok--hover;
    }

    .icon {
      color: $color__background;
      position:relative;
      top:-3px;
    }
  }
</style>
