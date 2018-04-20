<template>
  <div class="nested-datatable">
    <div class="nested-datatable__header">

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
  import NestedList from './NestedList'
  import draggable from 'vuedraggable'

  export default {
    name: 'A17NestedDatatable',
    mixins: [DatatableMixin, DraggableMixin, NestedDraggableMixin],
    data: function () {
      return {
        items: this.$store.state.datatable.data
      }
    },
    components: {
      'a17-nested-list': NestedList,
      draggable
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
  @import '~styles/setup/_mixins-colors-vars.scss';

  .nested-datatable__table {
    position: relative;
    width: 100%;
  }
</style>
