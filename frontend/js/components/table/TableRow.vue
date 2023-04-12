<template>
  <tr class="tablerow">
    <td v-for="col in columns" :key="col.name" class="tablecell" :class="cellClasses(col, 'tablecell')" :style="nestedStyle(col)">
      <template v-if="isSpecificColumn(col)">
        <component :is="currentComponent(col)"
                   v-bind="currentComponentProps(col)"
                   :row="row"
                   @update="tableCellUpdate"
                   @editInPlace="editInPlace"/>
      </template>
      <a17-table-cell-generic v-else v-bind="currentComponentProps(col)" :row="row" @editInPlace="editInPlace" @update="tableCellUpdate"/>
    </td>
    <td class="tablecell tablecell--spacer">&nbsp;</td>
    <td class="tablecell tablecell--sticky">
      <a17-table-cell-actions v-if="row.edit" v-bind="currentComponentProps()" @editInPlace="editInPlace" @update="tableCellUpdate" @restoreRow=" restoreRow" @destroyRow="destroyRow" @deleteRow="deleteRow" @duplicateRow="duplicateRow"/>
    </td>
  </tr>
</template>

<script>
  import TableCellComponents from '@/components/table/tableCell'
  import { DatatableRowMixin } from '@/mixins'

  export default {
    name: 'A17Tablerow',
    mixins: [DatatableRowMixin],
    components: {
      ...TableCellComponents
    },
    props: {
      draggable: {
        type: Boolean,
        default: false
      },
      nestedDepth: {
        type: Number,
        default: 0
      },
      rowType: {
        type: String,
        default: ''
      }
    },
    computed: {
      nestedOffset () {
        return this.columns.find((col) => col.name === 'draggable') ? 10 : 0
      }
    },
    methods: {
      nestedStyle (col) {
        return this.columns.find((col) => col.name === 'nested') && col.name === 'draggable' ? {
          'webkit-transform': 'translateX(-' + this.nestedDepth * 80 + 'px)',
          transform: 'translateX(-' + this.nestedDepth * 80 + 'px)'
        } : ''
      }
    }
  }
</script>

<style lang="scss" scoped>

  .tablerow {
    position: relative;
    border-bottom: 1px solid $color__border--light;

    &:hover {
      td {
        background-color: $color__f--bg;
      }
    }
  }

  /* Default cell */
  .tablecell {
    overflow: hidden;
    vertical-align: top;
    padding: 20px 10px;
    background-color: $color__background;
  }

  /* Icons */
  .tablecell--icon {
    width: 1px;
    padding-left: 10px;
    padding-right: 10px;
  }

  /* Bulk Edit checkboxes */
  .tablecell--bulk {
    width: 1px;
    padding-left: 10px;
    padding-right: 10px;

    &:first-child {
      padding-left: 20px;
    }
  }

  /* Thumb */
  .tablecell--thumb {
    width: 1px;

    @include breakpoint(xsmall) { // no thumbnail on smaller screens
      padding-left: 0;
      padding-right: 0;
    }
  }

  /* Spacer */
  .tablecell--spacer {
    width: 1px;
    padding-left: 25px;
    padding-right: 25px;
  }

  /* Sticky */
  .tablecell--sticky {
    position: absolute;
    right: 0;
    top: auto;
    padding: 15px 20px;
    background: linear-gradient(to right, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 1) 25%);
    overflow: visible;
  }

  tr:hover > .tablecell--sticky {
    background: linear-gradient(to right, #{rgba($color__f--bg, 0)} 0%, #{rgba($color__f--bg, 1)} 25%);
  }

  /* Draggable */
  .tablecell.tablecell--draggable {
    width: 10px;
    padding: 0;
    position: relative;

    + td {
      padding-left: 20px - 10px;
    }
  }

  tr:hover > .tablecell--draggable .tablecell__handle {
    display: block;
  }

  /* Nested table cell */
  .tablerow--nested {
    display: table;
    width: 100%;

    .tablecell.tablecell--draggable {
      position: absolute;
      top: 0;
      bottom: 0;
      transform: translateX(-80px);
    }

    .tablecell__handle {
      left: 0;
      margin-left: 0;
    }
  }

  .tablecell.tablecell--nested {
    position: absolute;
    height: calc(100% + 1px);
    padding: 20px 10px;
    border-bottom: 1px solid $color__border--light;
    overflow: auto;
    transform: translateX(-100%);

    &.tablecell--nested--parent {
      width: 0;
      padding: 0;
    }
  }
</style>
