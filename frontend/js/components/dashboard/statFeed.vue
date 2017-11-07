<template>
  <div class="box statFeed">
    <header class="box__header">
      <slot></slot>
    </header>
    <div class="box__body">
      <ul class="statFeed__list">
        <li>
          <a href="#" class="statFeed__item" target="_blank">
            <h3 class="f--heading">7K</h3>
            <div class="statFeed__info">
              <span class="statFeed__label">Users</span>
              <span class="statFeed__meta f--tiny">53% Bounce rate</span>
            </div>
          </a>
        </li>
        <li>
          <a href="#" class="statFeed__item" target="_blank">
            <h3 class="f--heading">8,4K</h3>
            <div class="statFeed__info">
              <span class="statFeed__label">Pageviews</span>
              <span class="statFeed__meta f--tiny">3,8 Pages / Session</span>
            </div>
          </a>
        </li>
        <li>
          <a href="#" class="statFeed__item" target="_blank">
            <h3 class="f--heading">15</h3>
            <div class="statFeed__info">
              <span class="statFeed__label">Contact requests</span>
              <span class="statFeed__meta f--tiny">5 Abandoned</span>
            </div>
          </a>
        </li>
      </ul>
    </div>
    <footer class="box__footer">
      <a href="#">Google Analytics</a>
    </footer>
  </div>
</template>

<script>
  export default {
    name: 'A17StatFeed',
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

  .statFeed {
    color:$color__stats;
  }

  .statFeed__item {
    padding:20px;
    border-top:1px solid $color__border;
    text-decoration:none;
    display:flex;
    color:$color__stats;
    @include monospaced-figures(off);

    &:hover {
      background: $color__ultralight;
    }
  }

  li:first-child .statFeed__item {
    border-top:0 none;
  }

  .statFeed__info {

  }
</style>
