<template>
  <div class="box statFeed">
    <header class="box__header">
      <b><slot></slot></b>
    </header>
    <div class="box__body">
      <template  v-for="fact in facts">
      <a :href="fact.url" class="statFeed__item" target="_blank">
        <h3 class="statFeed__numb f--heading">{{ fact.figure }}</h3>
        <div class="statFeed__info">
          <h4 class="statFeed__label">{{ fact.label }}</h4>
          <p class="statFeed__meta f--note f--small">{{ fact.insight }}</p>
        </div>
      </a>
      </template>
    </div>
    <footer class="box__footer statFeed__footer">
      <a href="#" class="f--external" target="_blank">Google Analytics</a>
    </footer>
  </div>
</template>

<script>
  export default {
    name: 'A17StatFeed',
    props: {
      facts: {
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
  @import '~styles/setup/_mixins-colors-vars.scss';

  .statFeed {

  }

  .statFeed__item {
    border-top:1px solid $color__border--light;
    text-decoration:none;
    padding:15px 0;
    display:flex;
    @include monospaced-figures(off);

    &:hover {
      background: $color__ultralight;
    }
  }

  .statFeed__numb {
    line-height:1em;
    min-width:33.333%;
  }

  .statFeed__item:first-child {
    border-top:0 none;
  }

  .statFeed__numb,
  .statFeed__footer {
    color:$color__stats;
  }

  .statFeed__numb,
  .statFeed__info {
    padding:10px 20px;
  }

  .statFeed__info {
    border-left:1px solid $color__border--light;
  }
</style>
