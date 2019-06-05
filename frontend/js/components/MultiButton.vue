<template>
  <div class="multibutton">
    <a17-dropdown ref="submitDown" position="bottom-right" width="full" :offset="0">
      <a17-button :type="type" @click="buttonClicked(options[0].name)" :name="options[0].name" variant="validate">{{ options[0].text }}</a17-button>
      <button class="multibutton__trigger" type="button" @click="$refs.submitDown.toggle()" v-if="otherOptions.length"><span v-svg symbol="dropdown_module"></span></button>

      <div slot="dropdown__content" v-if="otherOptions.length">
        <ul>
          <li v-for="option in otherOptions" :key="option.name">
            <button @click="buttonClicked(option.name)" :type="type" :name="option.name">{{ option.text }}</button>
          </li>
        </ul>
      </div>
    </a17-dropdown>
  </div>
</template>

<script>
  export default {
    name: 'A17Multibutton',
    props: {
      type: {
        default: 'button'
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
      }
    },
    methods: {
      buttonClicked: function (val) {
        this.$emit('button-clicked', val)
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

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
