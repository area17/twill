<template>
  <div class="pagenav">
    <div class="pagenav__form">
      <div class="form__field">
        <input type="search" :placeholder="placeholder" />
        <button type="button">search</button>
      </div>
    </div>
    <nav class="pagenav__nav">
      <a :href="previousUrl" class="pagenav__btn" v-if="previousUrl">← {{ previousLabel }}</a>
      <span v-else class="pagenav__btn">← {{ previousLabel }}</span>

      <a :href="nextUrl" class="pagenav__btn" v-if="nextUrl">{{ nextLabel }} →</a>
      <span v-else class="pagenav__btn">{{ nextLabel }} →</span>
    </nav>
  </div>

</template>

<script>
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
    },
    methods: {
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
    padding:20px;
  }

  .pagenav__nav {
    display:flex;
  }

  .pagenav__btn {
    border-top:1px solid $color__border--light;
    border-right:1px solid $color__border--light;
    padding:0 20px;
    flex: 1 0 0px;
    overflow: hidden;
    height:48px;
    line-height:48px;
    text-decoration:none;
    color:$color__text--light;

    &:last-child {
      border-right:0 none;
    }
  }

  a.pagenav__btn {
    &:hover {
      color:$color__text;
      background:$color__ultralight;
    }
  }

  .pagenav__btn + .pagenav__btn {
    text-align:right;
  }

</style>
