<template>
  <div class="box popularFeed">
    <header class="box__header">
      <b><slot></slot></b>
    </header>
    <div class="box__body">
      <ol class="popularFeed__list">
        <li v-for="entity in entities">
          <a :href="entity.url" class="popularFeed__item" target="_blank">
            <span class="popularFeed__label"><span>{{ entity.name }}</span></span>
            <span class="popularFeed__views f--tiny">{{ entity.number }}</span>
          </a>
        </li>
      </ol>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'A17PopularFeed',
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
  @import '~styles/setup/_mixins-colors-vars.scss';

  .popularFeed {
  }

  .popularFeed__list {
    list-style-type: none;
    counter-reset: references;
  }

  .popularFeed__item {
    padding:20px;
    border-top:1px solid $color__border--light;
    text-decoration:none;
    display:flex;

    &::before {
      counter-increment: references;
      content: counter(references);
      margin-right:13px;
      @include monospaced-figures(off);
    }

    &:hover {
      background: $color__ultralight;

      .popularFeed__label span {
        @include bordered($color__link, false);
      }
    }
  }

  li:first-child .popularFeed__item {
    border-top:0 none;
  }

  .popularFeed__label {
    flex-grow:1;
    color:$color__link;
  }

  .popularFeed__views {
    color:$color__text--light;
  }
</style>
