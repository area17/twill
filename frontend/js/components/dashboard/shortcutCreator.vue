<template>
  <div class="shortcutCreator">
    <div class="container" v-if="entities.length">
      <div class="wrapper wrapper--reverse">
        <div class="shortcutCreator__create">
          <a17-dropdown class="shortcutCreator__dropdown" ref="createNewDropdown" position="bottom-right" width="full" :offset="0">
            <a17-button type="button" variant="action" @click="$refs.createNewDropdown.toggle()">Create New</a17-button>
            <button type="button" class="shortcutCreator__trigger" @click="$refs.createNewDropdown.toggle()"><span v-svg symbol="dropdown_module"></span></button>

            <div slot="dropdown__content">
              <ul>
                <li v-for="entity in entities"><a href="#">{{ entity.singular }}</a></li>
              </ul>
            </div>
          </a17-dropdown>
        </div>
        <div class="shortcutCreator__listing">
          <a class="shortcutCreator__listingItem" href="#" v-for="entity in entities">

            <span class="shortcutCreator__label">{{ entity.label }}</span>
            <h3 class="shortcutCreator__sum f--heading">{{ entity.number }}</h3>
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'A17ShortcutCreator',
    props: {
      entities: {
        type: Array,
        default: function () {
          return []
        }
      }
    },
    computed: {
    },
    methods: {
    }
  }
</script>

<style lang="scss" scoped>
  @import "../../../scss/setup/variables.scss";
  @import "../../../scss/setup/colors.scss";
  @import "../../../scss/setup/mixins.scss";

  $height_btn: 40px;

  $column-spans__listing: (
    xsmall: 6,
    small: 6,
    medium: 3,
    large: 4,
    xlarge: 5
  );

  $column-spans__item: (
    xsmall: 6,
    small: 2,
    medium: 1,
    large: 1,
    xlarge: 1
  );

  $column-spans__button: (
    xsmall: 6,
    small: 6,
    medium: 3,
    large: 1,
    xlarge: 1
  );

  .shortcutCreator {
    padding:20px 0;
    width:100%;
    background-color:$color__border--light;
    border-bottom:1px solid $color__border;
  }

  .shortcutCreator__listing {
    display:flex;
    flex-grow:1;
    flex-flow: column nowrap;

    @include breakpoint('small+') {
      flex-flow: row nowrap;
    }
  }

  .shortcutCreator__listingItem {
    text-decoration:none;
    padding:20px 0 0;
    @include column-flex($column-spans__item);

    @include breakpoint('small+') {
      padding:5px 0 7px;
    }

    &:hover {
      color:$color__link;
    }
  }

  .shortcutCreator__label {
    padding-bottom:7px;
    display:block;
  }

  .shortcutCreator__sum {
    line-height: 1em;
    @include monospaced-figures(off);
  }

  @include breakpoint('medium+') {
    .shortcutCreator__listingItem {
      border-right:1px solid $color__border;

      &:last-child {
        border-right:0 none;
      }
    }
  }

  .shortcutCreator__create {
    display:flex;
    @include column-flex($column-spans__button);

    .dropdown {
      width: 100%;
      height: $height_btn; // calc(100% - 40px);

      @include breakpoint('small+') {
        margin: 20px 0;
      }

      > button:first-child {
        flex-grow:1;
        padding-left:0;
        padding-right:0;
      }
    }
  }

  .shortcutCreator__trigger {
    @include btn-reset;
    height:$height_btn;
    line-height:$height_btn;
    text-align:center;
    border-top-right-radius:2px;
    border-bottom-right-radius:2px;
    background:$color__action;
    color: $color__background;
    margin-left: -2px;
    border-left:1px solid $color__action--hover;
    padding:0 10px;
    transition: color .2s linear, border-color .2s linear, background-color .2s linear;

    &:hover,
    &:focus {
      background:$color__action--hover;
    }

    .icon {
      color: $color__background;
    }
  }

  button:focus + .shortcutCreator__trigger,
  button:hover + .shortcutCreator__trigger {
    background:$color__action--hover;
  }
</style>
