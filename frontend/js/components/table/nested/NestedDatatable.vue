<template>
  <div class="nested-datatable">
    <!-- Actual table content -->
    <!--Todo: refactor to remove table-->
    <div class="container">
      <div class="datatable__table">
        <a17-table>
          <thead>
          <a17-tablehead :columns="visibleColumns" ref="thead"/>
          </thead>
        </a17-table>
      </div>
    </div>

    <div class="container">
      <div class="nested-datatable__table">
        <a17-nested-list
          :nested="true"
          :maxDepth="maxDepth"
          :draggable="draggable"
        />
      </div>
    </div>
  </div>
</template>

<script>
  import { DatatableMixin, DraggableMixin, NestedDraggableMixin } from '@/mixins/index'
  import { DATATABLE } from '@/store/mutations/index'

  import a17Table from './../Table.vue'
  import a17Tablehead from './../TableHead.vue'
  import NestedList from './NestedList'

  export default {
    name: 'A17NestedDatatable',
    mixins: [DatatableMixin, DraggableMixin, NestedDraggableMixin],
    data: function () {
      return {
        items: this.$store.state.datatable.data
      }
    },
    components: {
      'a17-table': a17Table,
      'a17-tablehead': a17Tablehead,
      'a17-nested-list': NestedList
    },
    beforeMount: function () {
      function findBulkColumn (column) {
        return column.name === 'bulk'
      }

      function findDraggableColumn (column) {
        return column.name === 'draggable'
      }

      // bulk edit column
      if (this.bulkeditable) {
        if (!this.columns.find(findBulkColumn)) {
          this.$store.commit(DATATABLE.ADD_DATATABLE_COLUMN, {
            index: 0,
            data: {
              name: 'bulk',
              label: '',
              visible: true,
              optional: false,
              sortable: false
            }
          })
        }
      }

      if (this.draggable) {
        if (!this.columns.find(findDraggableColumn)) {
          this.$store.commit(DATATABLE.ADD_DATATABLE_COLUMN, {
            index: 0,
            data: {
              name: 'draggable',
              label: '',
              visible: true,
              optional: false,
              sortable: false
            }
          })
        }
      }
    }
  }
</script>

<style lang="scss" scoped>

  .nested-datatable__table {
    position: relative;
    width: 100%;
  }
</style>
