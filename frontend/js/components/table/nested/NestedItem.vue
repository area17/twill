<template>
  <div class="nested-item">
    <span v-for="col in columns" :key="col.name" class="nested-item__cell" :class="cellClasses(col, 'nested-item__cell')">
      <template v-if="isSpecificColumn(col)">
        <component :is="currentComponent(col)"
                   v-bind="currentComponentProps(col)"
                   @update="tableCellUpdate"
                   @editInPlace="editInPlace"/>
      </template>
      <a17-table-cell-generic v-else v-bind="currentComponentProps(col)" @editInPlace="editInPlace" @update="tableCellUpdate"/>
    </span>
    <span class="nested-item__cell nested-item__cell--actions">
      <a17-table-cell-actions v-bind="currentComponentProps()" @editInPlace="editInPlace" @update="tableCellUpdate" @restoreRow=" restoreRow" @deleteRow="deleteRow" @destroyRow="destroyRow" @duplicateRow="duplicateRow"/>
    </span>
  </div>
</template>

<script>
  import TableCellComponents from '@/components/table/tableCell'
  import { DatatableRowMixin } from '@/mixins'

  export default {
    name: 'A17-nested-item',
    mixins: [DatatableRowMixin],
    components: {
      ...TableCellComponents
    }
  }
</script>

<style lang="scss" scoped>

  .nested-item {
    position: relative;
    display: flex;
    padding: 0 10px;
    // margin-bottom: 10px;
    // border: 1px solid #f2f2f2;
    border-radius: 2px;

    &:hover {
      background-color: $color__f--bg;
    }
  }

  .nested-item__cell {
    position: relative;
    padding: 20px 10px;
    margin: 0 auto;
    flex-basis: 100%;

    &.nested-item__cell--icon,
    &.nested-item__cell--name,
    &.nested-item__cell--bulk,
    &.nested-item__cell--draggable,
    &.nested-item__cell--thumb {
      position: relative;
      flex-basis: 0;
      margin: 0;
    }

    &.nested-item__cell--name {
      flex-basis: auto;
    }

    &.nested-item__cell--actions {
      display: block;
      flex-basis: 0;
      padding: 20px 10px 0;
      margin: 0 0 0 auto;
    }

    &.nested-item__cell--draggable {
      position: absolute;
      top: 0;
      left: 0;
      bottom: 0;
    }
  }
</style>

<style lang="scss">
  .nested-item {
    &:hover {
      .nested-item__cell--draggable .tablecell__handle {
        display: block;
      }
    }

    .nested-item__cell {
      &.nested-item__cell--draggable {
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;

        .tablecell__handle {
          transform: translateX(-50%);
        }
      }
    }
  }
</style>
