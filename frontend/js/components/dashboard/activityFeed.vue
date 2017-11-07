<template>
  <div class="box activityFeed">
    <header class="box__header">
      <slot></slot>
    </header>
    <div class="box__body">
      <table class="activityFeed__list">
        <tr class="activityRow">
          <td class="activityCell activityCell--thumb">
            <img src="https://source.unsplash.com/random/50x50?sig=15" />
          </td>
          <td class="activityCell activityCell--icon">
          </td>
          <td class="activityCell">
            <a class="activityFeed__link" href="#">Garden Museum</a>
          </td>
        </tr>
        <tr class="activityRow">
          <td class="activityCell activityCell--thumb">
            <img src="https://source.unsplash.com/random/50x50?sig=7" />
          </td>
          <td class="activityCell activityCell--icon">
          </td>
          <td class="activityCell">
            <a class="activityFeed__link" href="#">'Red Trees'</a>
          </td>
        </tr>
        <tr class="activityRow">
          <td class="activityCell activityCell--thumb">
            <img src="https://source.unsplash.com/random/50x50?sig=9" />
          </td>
          <td class="activityCell activityCell--icon">
          </td>
          <td class="activityCell">
            <a class="activityFeed__link" href="#">Garden Museum</a>
          </td>
        </tr>
        <tr class="activityRow">
          <td class="activityCell activityCell--thumb">
            <img src="https://source.unsplash.com/random/50x50?sig=8" />
          </td>
          <td class="activityCell activityCell--icon">
          </td>
          <td class="activityCell">
            <a class="activityFeed__link" href="#">Garden Museum</a>
          </td>
        </tr>
        <tr class="activityRow">
          <td class="activityCell activityCell--thumb">
            <img src="https://source.unsplash.com/random/50x50?sig=5" />
          </td>
          <td class="activityCell activityCell--icon">
          </td>
          <td class="activityCell">
            <a class="activityFeed__link" href="#">Garden Museum</a>
          </td>
        </tr>
        <tr class="activityRow">
          <td class="activityCell activityCell--thumb">
            <img src="https://source.unsplash.com/random/50x50?sig=3" />
          </td>
          <td class="activityCell activityCell--icon">
          </td>
          <td class="activityCell">
            <a class="activityFeed__link" href="#">Garden Museum</a>
          </td>
        </tr>
        <tr class="activityRow">
          <td class="activityCell activityCell--thumb">
            <img src="https://source.unsplash.com/random/50x50?sig=4" />
          </td>
          <td class="activityCell activityCell--icon">
          </td>
          <td class="activityCell">
            <a class="activityFeed__link" href="#">Garden Museum</a>
          </td>
        </tr>
      </table>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'A17ActivityFeed',
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

  .activityFeed {
  }

  .activityFeed__list {
    overflow: hidden;
    width: 100%;
    border-collapse: collapse;
    border-spacing: 0;
  }

  .activityRow {
    border-bottom:1px solid $color__border--light;

    &:hover {
      td {
        background-color: $color__f--bg;
      }
    }
  }

  .activityCell {
    overflow: hidden;
    vertical-align: top;
    padding:15px 15px;
    background-color: $color__background;
  }

  .activityCell--thumb {
    width:1px;

    img {
      display:block;
      width:50px;
      min-height:50px;
      background:$color__border--light;
      height:auto;
    }
  }

  .activityCell--icon {
    width:1px;
  }

  .activityFeed__link {
    color:$color__link;
    text-decoration:none;

    &:hover {
      @include bordered($color__link, false);
    }
  }
</style>
