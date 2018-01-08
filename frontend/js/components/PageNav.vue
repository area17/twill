<template>
  <div class="pagenav">
    <div class="pagenav__form">
      <div class="form__field" :class="textfieldClasses">
        <input type="search" :placeholder="placeholder" />
        <button type="button">search</button>
      </div>
    </div>
    <nav class="pagenav__nav">
      <a href="#" class="pagenav__btn">← Previous page</a>
      <a href="#" class="pagenav__btn">Next page →</a>
    </nav>
  </div>

</template>

<script>
  import { mapState, mapGetters } from 'vuex'

  import a17Switcher from '@/components/Switcher.vue'
  import a17RadioAccordion from '@/components/RadioAccordion.vue'
  import a17ReviewAccordion from '@/components/ReviewAccordion.vue'
  import a17CheckboxAccordion from '@/components/CheckboxAccordion.vue'
  import a17RevisionAccordion from '@/components/RevisionAccordion.vue'
  import a17PubAccordion from '@/components/PubAccordion.vue'
  import a17MultiButton from '@/components/MultiButton.vue'

  import a17VueFilters from '@/utils/filters.js'

  export default {
    name: 'A17PageNav',
    props: {
      disabled: {
        type: Boolean,
        default: false
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

    &:hover {
      color:$color__text;
      background:$color__ultralight;
    }

    &:last-child {
      border-right:0 none;
    }
  }

  .pagenav__btn + .pagenav__btn {
    text-align:right;
  }

</style>
