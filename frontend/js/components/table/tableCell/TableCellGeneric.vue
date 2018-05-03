<template>
  <a17-table-cell-name v-if="colName === 'name'" v-bind="childProps" @update="update" @editInPlace="editInPlace"/>
  <a17-table-cell-html v-else-if="col.hasOwnProperty('html')" v-bind="childProps" @update="update" @editInPlace="editInPlace"/>
  <span v-else>{{ row[colName] }}</span>
</template>

<script>
  import A17TableCellName from './generic/TableCellName'
  import A17TableCellHTML from './generic/TableCellHTML'

  import { TableCellMixin } from '@/mixins'

  export default {
    name: 'A17TableCellGeneric',
    mixins: [TableCellMixin],
    computed: {
      childProps: function () {
        return this.$props
      }
    },
    components: {
      'a17-table-cell-name': A17TableCellName,
      'a17-table-cell-html': A17TableCellHTML
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

</style>
