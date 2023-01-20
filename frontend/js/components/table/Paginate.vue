<template>
  <div class="paginate">
    <p class="paginate__offset  f--small">
        {{ $trans('listing.paginate.rows-per-page') }}
        <template v-if="availableOffsets.length > 1">
          <a17-dropdown ref="paginateDropdown" position="bottom-right">
            <button @click="$refs.paginateDropdown.toggle()" class="paginate__button">{{ newOffset }}</button>
            <div slot="dropdown__content">
              <button type="button" v-for="availableOffset in availableOffsets" :key="availableOffset" :class="{ 'dropdown__active' : availableOffset === newOffset }" @click="changeOffset(availableOffset)">{{ availableOffset }}</button>
            </div>
          </a17-dropdown>
        </template>
        <template v-else>
          {{ newOffset }}
        </template>
    </p>
    <div class="paginate__pages" v-if="max > 1">
      <p class="paginate__current f--small"><input class="form__input paginate__input" type="number" v-model="newPageFormat" maxlength="4" @blur="formatPage" /> of {{ max }}</p>
      <button type="button" :disabled="value <= min"  class="paginate__prev" @click="previousPage"><span v-svg symbol="pagination_left"></span></button>
      <button type="button" :disabled="value >= max"  class="paginate__next" @click="nextPage"><span v-svg symbol="pagination_right"></span></button>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'A17Paginate',
    props: {
      value: {
        type: Number,
        required: true
      },
      offset: {
        type: Number,
        default: 60
      },
      availableOffsets: {
        type: Array,
        default: function () { return [] }
      },
      min: {
        type: Number,
        default: 1
      },
      max: {
        type: Number,
        required: true
      }
    },
    data () {
      return {
        newOffset: this.offset
      }
    },
    computed: {
      newPageFormat: {
        get: function () {
          return this.value
        },
        set: function (value) {
          return parseInt(value)
        }
      }
    },
    methods: {
      formatPage: function (event) {
        let newValue = event.target.value
        newValue = newValue !== '' ? parseInt(newValue) : 1

        if (newValue > this.max) newValue = this.max
        if (newValue < 1) newValue = 1

        event.target.value = newValue
        if (newValue !== this.value) this.$emit('changePage', newValue)
      },
      changeOffset: function (offset) {
        this.newOffset = offset

        this.$emit('changeOffset', parseInt(this.newOffset))
      },
      previousPage: function () {
        this.$emit('changePage', parseInt(this.value - 1))
      },
      nextPage: function () {
        this.$emit('changePage', parseInt(this.value + 1))
      }
    }
  }
</script>

<style lang="scss" scoped>

  .paginate {
    // border-top:1px solid $color__border--light;
    color:$color__text--light;
    padding:27px 20px 25px 20px;
    display:flex;
    flex-flow: row wrap;
  }

  // .paginate__pages {
  // }

  .paginate__current {
    display:inline-block;
    height:28px;
    line-height: 28px;
  }

  .paginate__offset {
    display:block;
    flex-grow:1;
    height:28px;
    line-height: 28px;

    .dropdown {
      display:inline-block;
    }
  }

  .paginate__button {
    @include btn-reset;
    color:$color__text--light;

    &::after {
      content:'';
      display:inline-block;
      width: 0;
      height: 0;
      margin-top: -1px;
      border-width: 4px 4px 0;
      border-style: solid;
      border-color: $color__icons transparent transparent;
      position: relative;
      top: -3px;
      margin-left: 5px;
    }

    &:focus,
    &:hover {
      color:$color__text;

      &::after {
        border-color: $color__text transparent transparent;
      }
    }
  }

  .paginate__input {
    display:inline-block;
    padding:0 10px;
    height:28px;
    line-height: 28px;
    width:auto;
    max-width:(4*12px);
    font-size:13px;
    margin-right:6px;
  }

  .paginate__prev,
  .paginate__next {
    @include btn-reset;
    background: transparent;
    color:$color__icons;
    height:28px;
    line-height: 28px;
    display: inline-block;
    vertical-align: middle;
    margin-left:15px;

    .icon {
      display:block;
    }

    &:focus,
    &:hover {
      color:$color__text;
    }

    &:disabled {
      opacity:0.5;
      pointer-events: none;

      &:focus,
      &:hover {
        color:$color__icons;
      }
    }
  }
</style>
