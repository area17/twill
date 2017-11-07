<template>
  <div class="multibutton">
    <a17-dropdown ref="submitDown" position="bottom-right" width="full" :offset="0">
      <a17-button :type="type" :name="options[0].name" variant="validate">{{ options[0].text }}</a17-button>
      <button class="multibutton__trigger" type="button" @click="$refs.submitDown.toggle()"><span v-svg symbol="dropdown_module"></span></button>

      <div slot="dropdown__content">
        <ul>
          <li v-for="option in otherOptions">
            <button :type="type" :name="option.name">{{ option.text }}</button>
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
    }
  }
</script>

<style lang="scss" scoped>
  @import "../../scss/setup/variables.scss";
  @import "../../scss/setup/colors.scss";
  @import "../../scss/setup/mixins.scss";

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
    background:$color__ok;
    color: $color__background;
    margin-left: -2px;
    border-left:1px solid $color__ok--hover;
    padding:0 10px;

    &:hover {
      background:$color__ok--hover;
    }

    .icon {
      color: $color__background;
    }
  }

  // .multibutton__options {
  //   display:none;
  //   position:absolute;
  //   top:$height_btn;
  //   background:rgba($color__background,0.9);
  //   border-radius:2px;
  //   box-shadow:0 0px 8px rgba(0,0,0,0.3);
  //   padding:10px 0;
  //   left:0;
  //   right:0;

  //   button {
  //     width:100%;
  //     height:40px;
  //     line-height: 40px;
  //     background:transparent;
  //     outline: none;
  //     text-align:left;
  //     -webkit-appearance: none;
  //     cursor: pointer;
  //     border:0 none;
  //     color:$color__text--light;
  //     font-size:1em;
  //     display:block;
  //     width:100%;
  //     padding:0 15px;
  //     white-space: nowrap;
  //     text-overflow: ellipsis;
  //     overflow:hidden;

  //     &:hover {
  //       color:$color__text;
  //       background:$color__light;
  //     }
  //   }
  // }

  // .multibutton__dropdown:hover {
  //   .multibutton__trigger {
  //     background:$color__ok--hover;
  //   }

  //   .multibutton__options {
  //     display:block;
  //   }
  // }
</style>
