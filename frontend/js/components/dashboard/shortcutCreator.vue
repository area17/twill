<template>
  <div class="shortcutCreator">
    <div class="container" v-if="entities.length">
      <div class="wrapper wrapper--reverse">
        <div class="shortcutCreator__create">
          <a17-dropdown class="shortcutCreator__dropdown" ref="createNewDropdown" position="bottom-right" width="full"
                        :offset="0">
            <a17-button type="button" class="shortcutCreator__btn" variant="action"
                        @click="$refs.createNewDropdown.toggle()">
              {{ $trans('dashboard.create-new', 'Create new') }}
              <span class="shortcutCreator__trigger"><span v-svg symbol="dropdown_module"></span></span>
            </a17-button>
            <div slot="dropdown__content">
              <ul>
                <template v-for="(entity, index) in entities">
                  <li :key="index" v-if="entity.createUrl"><a :href="entity.createUrl">{{ entity.singular }}</a></li>
                </template>
              </ul>
            </div>
          </a17-dropdown>
        </div>
        <div class="shortcutCreator__listing">
          <template v-for="(entity, index) in entities">
            <a class="shortcutCreator__listingItem" :href="entity.url" v-if="entity.number" :key="index">
              <span class="shortcutCreator__label">{{ entity.label }}</span>
              <h3 class="shortcutCreator__sum f--heading">{{ entity.number }}</h3>
            </a>
          </template>
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
    computed: {},
    methods: {}
  }
</script>

<style lang="scss" scoped>

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
    padding: 20px 0;
    width: 100%;
    background-color: $color__border--light;
    border-bottom: 1px solid $color__border;
  }

  .shortcutCreator .wrapper--reverse {
    @include breakpoint('medium+') {
      flex-flow: row-reverse;
    }
  }

  .shortcutCreator__listing {
    display: flex;
    flex-grow: 1;
    flex-flow: column nowrap;

    @include breakpoint('small+') {
      flex-flow: row wrap;
    }
  }

  .shortcutCreator__listingItem {
    text-decoration: none;
    padding: 20px 0 0;
    @include column-flex($column-spans__item);

    @include breakpoint('small+') {
      padding: 5px 0 7px;
    }

    &:hover {
      color: $color__link;

      .shortcutCreator__label::after {
        content: "\2192";
        font-size: 15px;
        color: inherit;
        position: absolute;
        top: 0;
        /*bottom:0em;*/
        vertical-align: baseline;
        transform: translateX(50%);
        font-weight: 400;
      }
    }
  }

  .shortcutCreator__label {
    padding-bottom: 7px;
    display: block;
    position: relative;
  }

  .shortcutCreator__sum {
    line-height: 1em;
    @include monospaced-figures(off);
    font-weight: 600;
  }

  @include breakpoint('medium+') {
    .shortcutCreator__listingItem {
      border-right: 1px solid $color__border;

      &:last-child {
        border-right: 0 none;
      }
    }
  }

  .shortcutCreator__create {
    display: flex;
    @include column-flex($column-spans__button);

    .dropdown {
      width: 100%;
      height: $height_btn; // calc(100% - 40px);

      @include breakpoint('small+') {
        margin: 20px 0;
      }

      > button:first-child {
        flex-grow: 1;
        padding-left: 0;
        padding-right: 0;
      }
    }
  }

  .shortcutCreator__trigger {
    height: $height_btn;
    line-height: $height_btn;
    text-align: center;
    // border-top-left-radius:0;
    // border-bottom-left-radius:0;
    // border-top-right-radius:2px;
    // border-bottom-right-radius:2px;
    color: $color__background;
    transition: color .2s linear;
    padding-left: 6px;
    // margin-left: -2px;
    // padding:0 10px;
    // position:absolute;
    // right:0;
    // top:0;

    .icon {
      position: relative;
      top: -2px;
    }
  }

  button:focus + .shortcutCreator__trigger,
  button:hover + .shortcutCreator__trigger {
    background: $color__action--hover;
  }
</style>

<style lang="scss">
  .shortcutCreator {
    .shortcutCreator__btn {
      position: relative;
    }
  }
</style>
