<template>
  <div class="box genericFeed">
    <header class="box__header">
      <b><slot></slot></b>
    </header>
    <div class="box__body">
      <ol class="genericFeed__list" :class="{ 'genericFeed__list--numbered' : hasNumber }">
        <li v-for="entity in entities">
          <a :href="entity.url" class="genericFeed__item" :target="target(entity)">
            <span class="genericFeed__thumbnails" v-if="entity.thumbnail"><img :src="entity.thumbnail" /></span>
            <span class="genericFeed__label"><span><span class="genericFeed__hover">{{ entity.name }}</span></span></span>
            <span class="genericFeed__views f--tiny" v-if="entity.number">{{ entity.number }}</span>
            <span class="genericFeed__type" v-if="entity.type">{{ entity.type }}</span>
          </a>
        </li>
      </ol>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'A17GenericFeed',
    props: {
      entities: {
        type: Array,
        default: function () {
          return []
        }
      }
    },
    computed: {
      hasNumber: function () {
        return this.entities.filter(entitiy => entitiy.hasOwnProperty('number')).length
      }
    },
    methods: {
      target: function (entity) {
        return entity.hasOwnProperty('external') ? '_blank' : false
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .genericFeed {
  }

  .genericFeed__list {
    list-style-type: none;

  }

  .genericFeed__hover {
    display:inline;
  }

  .genericFeed__item {
    padding:20px;
    border-top:1px solid $color__border--light;
    text-decoration:none;
    display:flex;
    background-color:$color__background;
    // transition: background-color .25s linear;

    &:hover {
      background-color: $color__ultralight;

      .genericFeed__hover {
        @include bordered($color__link, false);
      }
    }
  }

  li:first-child .genericFeed__item {
    border-top:0 none;
  }

  .genericFeed__label {
    flex-grow:1;
    color:$color__link;
    display:flex;
    align-items: center;
    justify-content: space-between;
  }

  .genericFeed__thumbnails {
    padding-right:15px;

    img {
      display:block;
      width:50px;
      min-height:50px;
      background:$color__border--light;
      height:auto;
    }
  }

  .genericFeed__views {
    color:$color__text--light;
    padding-left:15px;
  }

  .genericFeed__type {
    color:$color__text--light;
    padding-left:15px;
  }

  /* With numbers */
  .genericFeed__list--numbered {
    counter-reset: references;

    .genericFeed__item {
      &::before {
        counter-increment: references;
        content: counter(references);
        margin-right:13px;
        @include monospaced-figures(off);
      }
    }
  }
</style>
