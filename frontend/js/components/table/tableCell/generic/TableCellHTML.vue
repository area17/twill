<template>
  <span class="tablecell__raw" ref="cell" v-html="row[colName]"></span>
</template>

<script>
  import TableCellMixin from '@/mixins/tableCell'

  export default {
    name: 'A1TableCellHtml',
    mixins: [TableCellMixin],
    mounted () {
      const editlink = this.$refs.cell
        .querySelector('a[data-edit="true"]')

      if (editlink) {
        editlink.addEventListener('click', this.preventEditInPlace)
      }
    }
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
