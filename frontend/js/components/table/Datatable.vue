<template>
  <div class="datatable" v-sticky data-sticky-id="thead" data-sticky-offset="0">

    <!-- Sticky table head -->
    <div class="datatable__sticky" data-sticky-top="thead">
      <div class="datatable__stickyHead" data-sticky-target="thead">
        <div class="container">
          <div class="datatable__stickyInner">
            <div class="datatable__setup">
              <a17-dropdown class="datatable__setupDropdown" v-if="hideableColumns.length" ref="setupDropdown"
                            position="bottom-right" :title="$trans('listing.columns.show')" :clickable="true" :offset="-10">
                <button class="datatable__setupButton" @click="$refs.setupDropdown.toggle()">
                  <span v-svg symbol="preferences"></span></button>
                <div slot="dropdown__content">
                  <a17-checkboxgroup name="visibleColumns" :options="checkboxesColumns" :selected="visibleColumnsNames"
                                     @change="updateActiveColumns" :min="2"/>
                </div>
              </a17-dropdown>
            </div>
            <div class="datatable__stickyTable">
              <a17-table :columnsWidth="columnsWidth" :xScroll="xScroll" @scroll="updateScroll">
                <thead>
                <a17-tablehead :columns="visibleColumns" @sortColumn="updateSort"/>
                </thead>
              </a17-table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Actual table content -->
    <div class="container">
      <div class="datatable__table" :class="isEmptyDatable">
        <a17-table :xScroll="xScroll" @scroll="updateScroll">
          <thead>
          <a17-tablehead :columns="visibleColumns" ref="thead"/>
          </thead>
          <template v-if="draggable">
            <draggable class="datatable__drag" :tag="'tbody'" v-model="rows" :options="dragOptions">
              <template v-for="(row, index) in rows">
                <a17-tablerow :row="row" :index="index" :columns="visibleColumns" :key="row.id"/>
              </template>
            </draggable>
          </template>

          <tbody v-else>
          <template v-for="(row, index) in rows">
            <a17-tablerow :row="row" :index="index" :columns="visibleColumns" :key="row.id"/>
          </template>
          </tbody>
        </a17-table>

        <template v-if="rows.length <= 0">
          <div class="datatable__empty">
            <h4>{{ emptyMessage }}</h4>
          </div>
        </template>
        <a17-paginate v-if="maxPage > 1 || initialMaxPage > maxPage && !isEmpty" :max="maxPage" :value="page"
                      :offset="offset" :availableOffsets="[initialOffset,initialOffset*3,initialOffset*6]"
                      @changePage="updatePage" @changeOffset="updateOffset"/>
      </div>
    </div>
    <a17-spinner v-if="loading">Loading&hellip;</a17-spinner>
  </div>
</template>

<script>
  import debounce from 'lodash/debounce'
  import draggable from 'vuedraggable'
  import { mapState } from 'vuex'

  import a17Spinner from '@/components/Spinner.vue'
  import { DatatableMixin, DraggableMixin } from '@/mixins'
  import ACTIONS from '@/store/actions'
  import { DATATABLE } from '@/store/mutations'

  import a17Paginate from './Paginate.vue'
  import a17Table from './Table.vue'
  import a17Tablehead from './TableHead.vue'
  import a17Tablerow from './TableRow.vue'

  export default {
    name: 'A17Datatable',
    components: {
      'a17-table': a17Table,
      'a17-tablehead': a17Tablehead,
      'a17-tablerow': a17Tablerow,
      'a17-paginate': a17Paginate,
      'a17-spinner': a17Spinner,
      draggable
    },
    mixins: [DatatableMixin, DraggableMixin],
    data: function () {
      return {
        handle: '.tablecell__handle',
        reorderable: !this.draggable,
        xScroll: 0,
        columnsWidth: []
      }
    },
    computed: {
      checkboxesColumns: function () {
        const checkboxes = []

        if (this.hideableColumns.length) {
          this.hideableColumns.forEach(function (column) {
            checkboxes.push({
              value: column.name,
              label: column.label
            })
          })
        }

        return checkboxes
      },
      ...mapState({
        page: state => state.datatable.page,
        offset: state => state.datatable.offset,
        maxPage: state => state.datatable.maxPage,
        initialOffset: state => state.datatable.defaultOffset,
        initialMaxPage: state => state.datatable.defaultMaxPage,
        loading: state => state.datatable.loading
      })
    },
    methods: {
      getColumnWidth: function () {
        const self = this
        const newColumnsWidth = []
        const tds = self.$refs.thead.$el.children

        for (let index = 0; index < tds.length; index++) {
          newColumnsWidth.push(tds[index].offsetWidth)
        }

        self.columnsWidth = newColumnsWidth
      },
      updateScroll: function (newValue) {
        this.xScroll = newValue
      },
      resize: debounce(function () {
        this.getColumnWidth()
      }, 100),
      initEvents: function () {
        const self = this
        window.addEventListener('resize', () => self.resize())
        self.resize()
      },
      disposeEvents: function () {
        const self = this
        window.removeEventListener('resize', self.resize())
      },
      updateSort: function (column) {
        if (!column.sortable) return

        // The listing should not be reordable if it is sorted
        if (this.reorderable) {
          this.reorderable = false
          this.$store.commit(DATATABLE.REMOVE_DATATABLE_COLUMN, 'draggable')
        }

        this.$store.commit(DATATABLE.UPDATE_DATATABLE_PAGE, 1)
        this.$store.commit(DATATABLE.UPDATE_DATATABLE_SORT, column)

        // reload datas
        this.$store.dispatch(ACTIONS.GET_DATATABLE)
      },
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
      },
      updateActiveColumns: function (values) {
        this.$store.commit(DATATABLE.UPDATE_DATATABLE_VISIBLITY, values)

        this.$nextTick(function () {
          this.getColumnWidth()
        })

        // reload datas
        this.$store.dispatch(ACTIONS.GET_DATATABLE)
      }
    },
    watch: {
      loading: function () {
        this.$nextTick(function () {
          this.getColumnWidth()
        })
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
    },
    mounted: function () {
      this.initEvents()
    },
    beforeDestroy: function () {
      this.disposeEvents()
    }
  }
</script>

<style lang="scss" scoped>

  table {
    width: 100%;
  }

  // .datatable {
  // }

  .datatable__table {
    border: 1px solid $color__border--light;
    border-radius: 2px;
    position: relative;
  }

  .datatable__setupDropdown {
    float: right;
    padding: 18px 20px 16px 15px;
    background: linear-gradient(to right, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 1) 25%);
  }

  .datatable__setupButton {
    @include btn-reset;
    color: $color--icons;
    padding: 0;

    &:focus,
    &:hover {
      color: $color--text;
    }
  }

  .datatable__setup {
    position: absolute;
    right: 0;
    width: 50px;
    top: 0;
    z-index: 1;
  }

  /* Empty datatable */
  .datatable__table--empty {
    border: none;
    border-top: 1px solid $color__border--light;
  }

  .datatable__empty {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 300px;
    min-height: calc(100vh - #{170px + 60px + 100px + 100px + 100px});
    padding: 15px 20px;

    h4 {
      @include font-medium();
      font-weight: 400;
      color: $color__f--text;
    }
  }

  /* Sticky table head */

  .datatable__sticky {
    height: 60px;
  }

  @include breakpoint('medium+') {
    .datatable__stickyHead {
      background-clip: padding-box;

      &.sticky__fixedTop {
        display: block;
        top: 0;

        background-color: rgba($color__border--light, 0.97);
        border-bottom: 1px solid rgba($color__black, 0.05);

        .datatable__setupDropdown {
          background: linear-gradient(to right, rgba($color__border--light, 0) 0%, $color__border--light 25%);
        }
      }
    }
  }

  .datatable__stickyHead {
    width: 100%;
    z-index: $zindex__stickyTableHead;
  }

  .datatable__stickyInner {
    position: relative;
  }

  .datatable__stickyTable {
    max-height: 60px;
    overflow: hidden;

    .table__scroller {
      padding-bottom: 50px;
    }
  }
</style>

<style lang="scss">
  .datatable__table {
    .table {
      margin-top: -60px; // hide the other thead
    }
  }
</style>
