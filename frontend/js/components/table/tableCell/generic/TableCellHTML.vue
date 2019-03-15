<template>
  <span class="tablecell__raw" v-html="row[colName]"></span>
</template>

<script>
  import { TableCellMixin } from '@/mixins'

  export default {
    name: 'A1TableCellHtml',
    mixins: [TableCellMixin]
  }
</script>

<style lang="scss">
  @import '~styles/setup/_mixins-colors-vars.scss';

  /* With HTML */
  .tablecell__raw a {
    text-decoration: none;
    cursor: pointer;
    @include bordered;
  }
</style>
