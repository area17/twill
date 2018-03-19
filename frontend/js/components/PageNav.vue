<template>
  <div class="pagenav" v-if="parents.length || hasUrl">
    <div class="pagenav__form" v-if="parents.length">
      <a17-vselect name="parents_sources" :placeholder="placeholder" size="large" :searchable="true" :options="options" @change="gotoUrl"></a17-vselect>
    </div>
    <nav class="pagenav__nav" v-if="hasUrl">
      <a :href="previousUrl" class="pagenav__btn" v-if="previousUrl">← {{ previousLabel }}</a>
      <span v-else class="pagenav__btn">← {{ previousLabel }}</span>

      <a :href="nextUrl" class="pagenav__btn" v-if="nextUrl">{{ nextLabel }} →</a>
      <span v-else class="pagenav__btn">{{ nextLabel }} →</span>
    </nav>
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import parentTreeToOptions from '@/utils/parentTreeToOptions.js'

  export default {
    name: 'A17PageNav',
    props: {
      previousLabel: {
        type: String,
        default: 'Previous page'
      },
      nextLabel: {
        type: String,
        default: 'Next page'
      },
      previousUrl: {
        type: String,
        default: ''
      },
      nextUrl: {
        type: String,
        default: ''
      },
      placeholder: {
        type: String,
        default: ''
      }
    },
    data: function () {
      return {
      }
    },
    computed: {
      hasUrl: function () {
        return this.previousUrl || this.nextUrl
      },
      options: function () {
        return parentTreeToOptions(this.parents, '–')
      },
      ...mapState({
        parents: state => state.parents.all
      })
    },
    methods: {
      gotoUrl: function (newValue) {
        if (newValue.edit) {
          window.location.href = newValue.edit
        }
      }
    },
    beforeMount: function () {
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .pagenav {
    border-radius:2px;
    border:1px solid $color__border;
    background:$color__background;
    margin-bottom:20px;
  }

  .pagenav__form {
    padding:0 20px 20px 20px;
    margin-top: -15px;
  }

  .pagenav__nav {
    display:flex;
  }

  .pagenav__form + .pagenav__nav {
    .pagenav__btn {
      border-top:1px solid $color__border--light;
    }
  }

  .pagenav__btn {
    border-right:1px solid $color__border--light;
    padding:0 20px;
    flex: 1 0 0px;
    overflow: hidden;
    height:48px;
    line-height:48px;
    text-decoration:none;
    color:$color__text--light;
    opacity:0.5;

    &:last-child {
      border-right:0 none;
    }
  }

  a.pagenav__btn {
    opacity:1;

    &:focus,
    &:hover {
      color:$color__text;
      background:$color__ultralight;
    }
  }

  .pagenav__btn + .pagenav__btn {
    text-align:right;
  }

</style>
