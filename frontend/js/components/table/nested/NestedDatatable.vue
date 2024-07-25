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
      
        <a17-paginate v-if="maxPage > 1 || initialMaxPage > maxPage && !isEmpty" :max="maxPage" :value="page"
          :offset="offset" :availableOffsets="[initialOffset,initialOffset*3,initialOffset*6]"
          @changePage="updatePage" @changeOffset="updateOffset"
        />
      </div>
    </div>
  </div>
</template>

<script>
  import { DatatableMixin, DraggableMixin, NestedDraggableMixin } from '@/mixins/index'
  import { DATATABLE } from '@/store/mutations/index'
  import ACTIONS from '@/store/actions'
  import { mapState } from 'vuex'

  import a17Table from './../Table.vue'
  import a17Tablehead from './../TableHead.vue'
  import a17Paginate from './../Paginate.vue'
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
      'a17-paginate': a17Paginate,
      'a17-nested-list': NestedList
    },
    computed: {
      ...mapState({
        page: state => state.datatable.page,
        offset: state => state.datatable.offset,
        maxPage: state => state.datatable.maxPage,
        initialOffset: state => state.datatable.defaultOffset,
        initialMaxPage: state => state.datatable.defaultMaxPage
      })
    },
    methods: {
      updateOffset: function (value) {
        this.$store.commit(DATATABLE.UPDATE_DATATABLE_PAGE, 1)
        this.$store.commit(DATATABLE.UPDATE_DATATABLE_OFFSET, value)

        // reload datas
        this.$store.dispatch(ACTIONS.GET_DATATABLE)
      },
      updatePage: function (value) {
        if (value !== this.page) {
          this.$store.commit(DATATABLE.UPDATE_DATATABLE_PAGE, value)

          // reload datas
          this.$store.dispatch(ACTIONS.GET_DATATABLE)
        }
      }
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

    .paginate {
      border: 1px solid #F2F2F2;
      border-top: 0;
    }
  }
</style>
