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

  /* With HTML */
  .tablecell__raw a {
    text-decoration: none;
    cursor: pointer;
    color: $color__link;
  }
</style>
