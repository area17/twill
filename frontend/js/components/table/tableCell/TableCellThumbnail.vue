<template>
  <a class="tablecell__thumb"
     v-if="!row.hasOwnProperty('deleted')"
     :href="editUrl"
     @click="preventEditInPlace($event)">
    <img :src="row[colName]"/>
  </a>
  <a class="tablecell__thumb"
     v-else>
    <img :src="row[colName]"/>
  </a>
</template>

<script>
  import { TableCellMixin } from '@/mixins'

  export default {
    name: 'A17TableCellThumbNail',
    mixins: [TableCellMixin]
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  /* Thumbnails */
  .tablecell--thumb {
    width: 1px;

    @include breakpoint(xsmall) { // no thumbnail on smaller screens
      padding-left: 0;
      padding-right: 0;
    }
  }

  .tablecell__thumb {
    display: block;

    @include breakpoint(xsmall) { // no thumbnail on smaller screens
      display: none;
    }

    img {
      display: block;
      width: 80px;
      min-height: 80px;
      background: $color__border--light;
      height: auto;
    }
  }
</style>
