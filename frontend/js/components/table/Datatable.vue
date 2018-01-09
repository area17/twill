<template>
  <div class="datatable" v-sticky data-sticky-id="thead" data-sticky-offset="0">

    <!-- Sticky table head -->
    <div class="datatable__sticky" data-sticky-top="thead" v-if="!nested">
      <div class="datatable__stickyHead" data-sticky-target="thead">
        <div class="container">
          <div class="datatable__stickyInner">
            <div class="datatable__setup">
              <a17-dropdown class="datatable__setupDropdown" v-if="hideableColumns.length" ref="setupDropdown" position="bottom-right" title="Show" :clickable="true" :offset="-10">
                <button class="datatable__setupButton" @click="$refs.setupDropdown.toggle()"><span v-svg symbol="preferences"></span></button>
                <div slot="dropdown__content">
                  <a17-checkboxgroup name="visibleColumns" :options="checkboxesColumns" :selected="visibleColumnsNames" @change="updateActiveColumns" :min="2"></a17-checkboxgroup>
                </div>
              </a17-dropdown>
            </div>
            <div class="datatable__stickyTable">
              <a17-table :columnsWidth="columnsWidth" :xScroll="xScroll" @scroll="updateScroll">
                <thead>
                  <a17-tablehead :columns="visibleColumns" @sortColumn="updateSort"></a17-tablehead>
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
            <a17-tablehead :columns="visibleColumns" ref="thead"></a17-tablehead>
          </thead>
          <template v-if="draggable">
            <draggable class="datatable__drag" :element="'tbody'" v-model="rows" :options="draggableOptions">
              <template v-for="(row, index) in rows">
                <a17-tablerow v-if="!nested" :row="row" :index="index" :columns="visibleColumns" :key="row.id"></a17-tablerow>
                <template v-else>
                  <tr class="tablerow-nested" :key="row.id">
                    <td :colspan="visibleColumns.length + 2">
                      <table>
                        <tbody>
                          <a17-tablerow :row="row" :index="index" :columns="visibleColumns" :draggable="draggable"></a17-tablerow>
                          <a17-tablerow-nested v-if="row.children" :maxDepth="nestedDepth" :parentId="row.id" :items="row.children" :columns="visibleColumns" :draggableOptions="draggableOptions"></a17-tablerow-nested>
                        </tbody>
                      </table>
                    </td>
                  </tr>
                </template>
              </template>
            </draggable>
          </template>

          <tbody v-else>
            <template v-for="(row, index) in rows">
              <a17-tablerow v-if="!nested" :row="row" :index="index" :columns="visibleColumns"
                            :key="row.id"></a17-tablerow>
              <template v-else>
                <tr class="tablerow-nested" :key="row.id">
                  <td :colspan="visibleColumns.length + 2">
                    <table>
                      <tbody>
                      <a17-tablerow :row="row" :index="index" :columns="visibleColumns"></a17-tablerow>
                      <a17-tablerow-nested v-if="row.children" :maxDepth="nestedDepth" :parentId="row.id" :items="row.children" :columns="visibleColumns"></a17-tablerow-nested>
                      </tbody>
                    </table>
                  </td>
                </tr>
              </template>
            </template>
          </tbody>
        </a17-table>

        <template v-if="rows.length <= 0">
          <div class="datatable__empty">
            <h4>{{ emptyMessage }}</h4>
          </div>
        </template>
        <a17-paginate v-if="maxPage > 1 || initialMaxPage > maxPage && !isEmpty" :max="maxPage" :value="page" :offset="offset" :availableOffsets="[initialOffset,initialOffset*3,initialOffset*6]" @changePage="updatePage" @changeOffset="updateOffset"></a17-paginate>
        <a17-spinner v-if="loading">Loading&hellip;</a17-spinner>
      </div>
    </div>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'

  import draggableMixin from '@/mixins/draggable'
  import draggable from 'vuedraggable'
  import debounce from 'lodash/debounce'

  import a17Table from './Table.vue'
  import a17Tablehead from './TableHead.vue'
  import a17Tablerow from './TableRow.vue'
  import a17TableRowNested from './TableRowNested.vue'
  import a17Paginate from './Paginate.vue'
  import a17Spinner from '@/components/Spinner.vue'

  export default {
    name: 'A17Datatable',
    components: {
      'a17-table': a17Table,
      'a17-tablehead': a17Tablehead,
      'a17-tablerow': a17Tablerow,
      'a17-tablerow-nested': a17TableRowNested,
      'a17-paginate': a17Paginate,
      'a17-spinner': a17Spinner,
      draggable
    },
    mixins: [draggableMixin],
    props: {
      draggable: {
        type: Boolean,
        default: false
      },
      bulkeditable: {
        type: Boolean,
        default: true
      },
      emptyMessage: {
        type: String,
        default: ''
      },
      name: {
        type: String,
        default: 'group1'
      },
      nested: {
        type: Boolean,
        default: false
      },
      nestedDepth: {
        type: Number,
        default: 1
      }
    },
    data: function () {
      return {
        willUpdate: true,
        xScroll: 0,
        columnsWidth: [],
        reorderable: false,
        dragAreas: null
      }
    },
    computed: {
      isEmpty: function () {
        return this.rows.length <= 0
      },
      isEmptyDatable: function () {
        return { 'datatable__table--empty': this.isEmpty }
      },
      rows: {
        get () {
          return this.$store.state.datatable.data
        },
        set (value) {
          // reorder rows
          this.$store.dispatch('setDatatableDatas', value)
        }
      },
      checkboxesColumns: function () {
        let checkboxes = []

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
      draggableOptions: function () {
        return {
          handle: '.tablecell__handle',
          disabled: !this.reorderable,
          group: {
            name: this.name
          }
        }
      },
      ...mapState({
        page: state => state.datatable.page,
        offset: state => state.datatable.offset,
        maxPage: state => state.datatable.maxPage,
        columns: state => state.datatable.columns,
        initialOffset: state => state.datatable.defaultOffset,
        initialMaxPage: state => state.datatable.defaultMaxPage,
        loading: state => state.datatable.loading
      }),
      ...mapGetters([
        'visibleColumns',
        'hideableColumns',
        'visibleColumnsNames'
      ])
    },
    methods: {
      getColumnWidth: function () {
        let self = this
        let newColumnsWidth = []

        if (self.$refs.thead) {
          // Get all the tds width (there must be a better way to do this) :
          const tds = self.$refs.thead.$el.children

          for (let index = 0; index < tds.length; index++) {
            newColumnsWidth.push(tds[index].offsetWidth)
          }
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
        let self = this
        window.addEventListener('resize', () => self.resize())
        self.resize()
      },
      disposeEvents: function () {
        let self = this
        window.removeEventListener('resize', self.resize())
      },
      updateSort: function (column) {
        if (!column.sortable) return

        // The listing should not be reordable if it is sorted
        if (this.reorderable) {
          this.reorderable = false
          this.$store.commit('removeDatableColumn', 'draggable')
        }

        this.$store.commit('updateDatablePage', 1)
        this.$store.commit('updateDatableSort', column)

        // reload datas
        this.$store.dispatch('getDatatableDatas')
      },
      updateOffset: function (value) {
        this.$store.commit('updateDatablePage', 1)
        this.$store.commit('updateDatableOffset', value)

        // reload datas
        this.$store.dispatch('getDatatableDatas')
      },
      updatePage: function (value) {
        this.$store.commit('updateDatablePage', value)

        // reload datas
        this.$store.dispatch('getDatatableDatas')
      },
      updateActiveColumns: function (values) {
        this.$store.commit('updateDatableVisibility', values)

        this.$nextTick(function () {
          this.getColumnWidth()
        })

        // reload datas
        this.$store.dispatch('getDatatableDatas')
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
      // bulk edit column
      const bulkColumn = {
        name: 'bulk',
        label: '',
        visible: true,
        optional: false,
        sortable: false
      }

      if (this.bulkeditable) {
        this.$store.commit('addDatableColumn', {
          index: 0,
          data: bulkColumn
        })
      }

      // Nested Column
      const nestedColumn = {
        name: 'nested',
        label: '',
        visible: true,
        optional: false,
        sortable: false
      }

      if (this.nested) {
        this.$store.commit('addDatableColumn', {
          index: 0,
          data: nestedColumn
        })
      }

      this.reorderable = this.draggable
      // draggable column
      const draggableColumn = {
        name: 'draggable',
        label: '',
        visible: true,
        optional: false,
        sortable: false
      }

      if (this.reorderable) {
        this.$store.commit('addDatableColumn', {
          index: 0,
          data: draggableColumn
        })
      }
    },
    mounted: function () {
      if (!this.nested) this.initEvents()
    },
    beforeDestroy: function () {
      if (!this.nested) this.disposeEvents()
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  table {
    width: 100%;
  }

  .datatable {

  }

  .datatable__table {
    border: 1px solid $color__border--light;
    border-radius: 2px;
    position: relative;

    /deep/ .table {
      margin-top: -60px; // hide the other thead
    }
  }

  .datatable__setupDropdown {
    float: right;
    padding: 18px 20px 16px 15px;
    background: linear-gradient(to right, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 1) 25%);
  }

  .datatable__setupButton {
    @include btn-reset;
    color: $color--icons;
    padding:0;

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

  /* Empty datable */
  .datatable__table--empty {
    border: none;
    border-top: 1px solid $color__border--light;
  }

  .datatable__empty {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 300px;
    min-height:calc(100vh - #{170px + 60px + 100px + 100px + 100px});
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
