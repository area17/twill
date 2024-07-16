<template>
  <a17-table-cell-html v-if="col.hasOwnProperty('html')" v-bind="childProps" @update="update" @editInPlace="editInPlace"/>
  <span v-else :class="{'truncate': col.hasOwnProperty('truncate') && col.truncate}" >{{ row[colName] }}</span>
</template>

<script>
  import TableCellMixin from '@/mixins/tableCell'

  import A17TableCellHTML from './generic/TableCellHTML'

  export default {
    name: 'A17TableCellGeneric',
    mixins: [TableCellMixin],
    computed: {
      childProps: function () {
        return this.$props
      }
    },
    components: {
      'a17-table-cell-html': A17TableCellHTML
    }
  }
</script>

<style lang="scss" scoped>
.truncate {
  text-overflow: ellipsis;
  display: inline-block;
  width: 85%;
  white-space: nowrap;
  overflow: hidden;
}
</style>
