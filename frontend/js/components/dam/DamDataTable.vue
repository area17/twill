<template>
  <div class="datatable">
    <a17-table
      :columnsWidth="columnsWidth"
      :xScroll="xScroll"
      @scroll="updateScroll"
    >
      <thead ref="thead">
        <tr class="tablehead">
          <td
            class="tablehead__cell f--small"
            v-for="col in visibleColumns"
            @click="sortColumn(col)"
            :key="col.name"
            :class="cellClasses(col)"
          >
            <span v-if="isDisplayedColumn(col)"
              >{{ col.label }}

              <span class="tablehead__arrow">↓</span></span
            >
            <a
              v-if="col.name === 'bulk'"
              href="#"
              @click.prevent.stop="toggleBulkSelect()"
              ><span
                ><a17-checkbox
                  name="bulkAll"
                  :value="1"
                  :initialValue="bulkValue"
                  :class="{ 'checkbox--minus': checkboxMinus }"
                ></a17-checkbox></span
            ></a>
          </td>
          <td class="tablehead__spacer">&nbsp;</td>
        </tr>
      </thead>
    </a17-table>

    <div class="datatable__table" :class="isEmptyDatable">
      <a17-table
        :xScroll="xScroll"
        @scroll="updateScroll"
        :columnsWidth="columnsWidth"
      >
        <tbody>
          <a17-tablerow
            v-for="(row, index) in localRows"
            :key="row.id"
            :row="row"
            :index="index"
            :columns="visibleColumns"
          />
        </tbody>
      </a17-table>

      <template v-if="localRows.length <= 0">
        <div class="datatable__empty">
          <h4>{{ emptyMessage }}</h4>
        </div>
      </template>
      <a17-paginate
        v-if="maxPage > 1 || (initialMaxPage > maxPage && !isEmpty)"
        :max="maxPage"
        :value="page"
        :offset="offset"
        :availableOffsets="[
          initialOffset,
          initialOffset * 3,
          initialOffset * 6
        ]"
        @changePage="updatePage"
        @changeOffset="updateOffset"
      />
    </div>
    <a17-spinner v-if="loading">Loading&hellip;</a17-spinner>
  </div>
</template>

<script>
  import debounce from 'lodash/debounce'
  import a17Paginate from '@/components/table/Paginate.vue'
  import a17Table from '@/components/table/Table.vue'
  import a17Tablerow from '@/components/table/TableRow.vue'
  import a17Spinner from '@/components/Spinner.vue'

  export default {
    name: 'A17DamDataTable',
    components: {
      'a17-table': a17Table,
      'a17-tablerow': a17Tablerow,
      'a17-paginate': a17Paginate,
      'a17-spinner': a17Spinner
    },
    props: {
      hideNames: {
        type: Boolean,
        default: true
      },
      rows: {
        type: Array,
        default: () => []
      },
      visibleColumns: {
        type: Array,
        default: () => [
          // { name: 'bulk' },
          { label: 'Image', name: 'thumbnail' },
          { label: 'Filename', sortable: true, name: 'name' },
          { label: 'Title', name: 'title' },
          { label: 'Description', name: 'description' },
          { label: 'Type', name: 'type' },
          { label: 'File size', name: 'size' }
        ]
      },
      page: {
        type: Number,
        default: 1
      },
      offset: {
        type: Number,
        default: 10
      },
      maxPage: {
        type: Number,
        default: 1
      },
      initialOffset: {
        type: Number,
        default: 10
      },
      initialMaxPage: {
        type: Number,
        default: 1
      },
      loading: {
        type: Boolean,
        default: false
      },
      emptyMessage: {
        type: String,
        default: 'No data available'
      }
    },
    data() {
      return {
        handle: '.tablecell__handle',
        xScroll: 0,
        columnsWidth: [],
        bulkIds: [],
        sortKey: null,
        sortDir: null
      }
    },
    watch: {
      loading() {
        this.$nextTick(this.getColumnWidth)
      }
    },
    computed: {
      bulkValue: function() {
        return this.bulkIds.length ? 1 : 0
      },
      checkboxMinus: function() {
        return (
          this.bulkIds.length > 0 && this.bulkIds.length !== this.dataIds.length
        )
      },
      isEmptyDatable() {
        return this.localRows.length === 0 ? 'datatable__table--empty' : ''
      },
      localRows() {
        return this.rows.map(row => ({
          name: row.name,
          thumbnail: row.thumbnail,
          title: row.metadatas.default.title,
          description: row.metadatas.default.description,
          type: row.fileExtension,
          size: ''
        }))
      }
    },
    methods: {
      cellClasses: function(col) {
        return [
          col.name === 'featured' || col.name === 'published'
            ? 'tablehead__cell--icon'
            : '',
          col.name === 'thumbnail' ? 'tablehead__cell--thumb' : '',
          col.name === 'thumbnail' &&
            col.variation &&
            col.variation === 'rounded'
            ? 'tablehead__cell--thumb-rounded'
            : '',
          col.name === 'draggable' ? 'tablehead__cell--draggable' : '',
          col.name === 'nested' ? 'tablehead__cell--nested' : '',
          col.name === 'bulk' ? 'tablehead__cell--bulk' : '',
          col.sortable ? 'tablehead__cell--sortable' : '',
          col.name === this.sortKey ? 'tablehead__cell--sorted' : '',
          col.name === this.sortKey && this.sortDir
            ? `tablehead__cell--sorted${this.sortDir}`
            : '',
          col.shrink === true ? 'tablehead__cell--shrink' : ''
        ]
      },
      isDisplayedColumn: function(col) {
        return (
          col.name !== 'draggable' &&
          col.name !== 'featured' &&
          col.name !== 'nested' &&
          col.name !== 'bulk' &&
          col.name !== 'published' &&
          col.name !== 'thumbnail'
        )
      },
      sortColumn: function(column) {
        if (column.sortable && this.sortable) this.$emit('sortColumn', column)
      },
      getColumnWidth() {
        const newColumnsWidth = []
        const tds = this.$refs.thead.querySelectorAll('td')
        for (let index = 0; index < tds.length; index++) {
          newColumnsWidth.push(tds[index].offsetWidth)
        }
        this.columnsWidth = newColumnsWidth
      },
      updateScroll(newValue) {
        this.xScroll = newValue
      },
      resize: debounce(function() {
        this.getColumnWidth()
      }, 100),
      initEvents() {
        window.addEventListener('resize', this.resize)
        this.resize()
      },
      disposeEvents() {
        window.removeEventListener('resize', this.resize)
      },
      updateSort(column) {
        // Sorting logic here
        this.$emit('sort-column', column)
      },
      updateOffset(value) {
        // Offset updating logic here
        this.$emit('update-offset', value)
      },
      updatePage(value) {
        // Page updating logic here
        this.$emit('update-page', value)
      }
    },
    mounted() {
      this.initEvents()
    },
    beforeDestroy() {
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
    background: linear-gradient(
      to right,
      rgba(255, 255, 255, 0) 0%,
      rgba(255, 255, 255, 1) 25%
    );
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
          background: linear-gradient(
            to right,
            rgba($color__border--light, 0) 0%,
            $color__border--light 25%
          );
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

  .tablehead__cell {
    color: $color__text--light;
    white-space: nowrap;
    vertical-align: top;
    padding: 20px 10px;

    &:hover {
      color: $color__text;
    }

    &--shrink {
      width: 1px;
    }
  }

  .tablehead__arrow {
    transition: all 0.2s linear;
    transform: rotate(0deg);
    opacity: 0;
    display: inline-block;
    margin-left: 10px;
    position: relative;
    top: -1px;

    // .icon {
    //   display:block;
    // }
  }

  .tablehead__spacer {
    width: 1px;
    padding-left: 25px;
    padding-right: 25px;
  }

  .tablehead__cell--draggable,
  .tablehead__cell--nested {
    padding: 0;
  }

  /* Thumbnails */
  .tablehead__cell--thumb,
  .tablehead__cell--icon,
  .tablehead__cell--draggable,
  .tablehead__cell--nested,
  .tablehead__cell--bulk {
    width: 1px;

    .tablehead__arrow {
      display: none;
    }
  }

  .tablehead__cell--draggable {
    width: 10px;
  }

  .tablehead__cell--bulk {
    width: 15px + 20px;
  }

  .tablehead__cell--thumb {
    width: 80px + 20px;

    @include breakpoint(xsmall) {
      // no thumbnail on smaller screens
      width: 1px;
      padding-left: 0;
      padding-right: 0;
    }
  }

  .tablehead__cell--thumb-rounded {
    width: 36px + 20px;

    @include breakpoint(xsmall) {
      // no thumbnail on smaller screens
      width: 1px;
      padding-left: 0;
      padding-right: 0;
    }
  }

  .tablehead__cell--icon {
    width: 10px + 20px;
  }

  .tablehead__cell--bulk {
    border-left: 1px solid transparent;
    padding-left: 10px;
    padding-right: 10px;

    a,
    .checkbox {
      display: block;
      width: 15px;
    }

    &:first-child {
      padding-left: 20px;
    }
  }

  .tablehead__cell--sortable {
    cursor: pointer;

    &:hover .tablehead__arrow {
      opacity: 1;
    }

    &.tablehead__cell--sorted {
      color: $color__text;

      .tablehead__arrow {
        opacity: 1;
      }
    }
  }

  .tablehead__cell--sorteddesc .tablehead__arrow {
    transform: rotate(180deg);
  }
</style>
