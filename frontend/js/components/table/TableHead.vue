<template>
  <tr class="tablehead">
    <td class="tablehead__cell f--small" v-for="col in columns" @click="sortColumn(col)" :key="col.name" :class="cellClasses(col)">
      <span v-if="isDisplayedColumn(col)">{{ col.label }} <span class="tablehead__arrow">↓</span></span>
      <a v-if="col.name === 'bulk'" href="#" @click.prevent.stop="toggleBulkSelect()"><span><a17-checkbox name="bulkAll" :value="1" :initialValue="bulkValue" :class="{ 'checkbox--minus' : checkboxMinus }" ></a17-checkbox></span></a>
    </td>
    <td class="tablehead__spacer">&nbsp;</td>
  </tr>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import { DATATABLE } from '@/store/mutations'

  export default {
    name: 'A17Tablehead',
    props: {
      sortable: {
        type: Boolean,
        default: true
      },
      columns: {
        type: Array,
        default: function () { return [] }
      }
    },
    data: function () {
      return {
        currentSort: 'name',
        currentDirection: 'asc' // asc or desc
      }
    },
    computed: {
      bulkValue: function () {
        return this.bulkIds.length ? 1 : 0
      },
      checkboxMinus: function () {
        return this.bulkIds.length > 0 && this.bulkIds.length !== this.dataIds.length
      },
      ...mapState({
        bulkIds: state => state.datatable.bulk,
        sortKey: state => state.datatable.sortKey,
        sortDir: state => state.datatable.sortDir
      }),
      ...mapGetters([
        'dataIds'
      ])
    },
    methods: {
      cellClasses: function (col) {
        return [
          col.name === 'featured' || col.name === 'published' ? 'tablehead__cell--icon' : '',
          col.name === 'thumbnail' ? 'tablehead__cell--thumb' : '',
          col.name === 'draggable' ? 'tablehead__cell--draggable' : '',
          col.name === 'nested' ? 'tablehead__cell--nested' : '',
          col.name === 'bulk' ? 'tablehead__cell--bulk' : '',
          col.sortable && this.sortable ? 'tablehead__cell--sortable' : '',
          col.name === this.sortKey ? `tablehead__cell--sorted` : '',
          col.name === this.sortKey && this.sortDir ? `tablehead__cell--sorted${this.sortDir}` : ''
        ]
      },
      isDisplayedColumn: function (col) {
        return col.name !== 'draggable' &&
          col.name !== 'featured' &&
          col.name !== 'nested' &&
          col.name !== 'bulk' &&
          col.name !== 'published' &&
          col.name !== 'thumbnail'
      },
      sortColumn: function (column) {
        if (column.sortable && this.sortable) this.$emit('sortColumn', column)
      },
      toggleBulkSelect: function () {
        const newBulkIds = (this.bulkIds.length) ? [] : this.dataIds
        this.$store.commit(DATATABLE.REPLACE_DATATABLE_BULK, newBulkIds)
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .tablehead__cell {
    color:$color__text--light;
    white-space: nowrap;
    vertical-align: top;
    padding:20px 10px;

    &:hover {
      color:$color__text;
    }
  }

  .tablehead__arrow {
    transition: all .2s linear;
    transform:rotate(0deg);
    opacity:0;
    display: inline-block;
    margin-left:10px;
    position:relative;
    top:-1px;

    // .icon {
    //   display:block;
    // }
  }

  .tablehead__spacer {
    width:1px;
    padding-left:25px;
    padding-right:25px;
  }

  .tablehead__cell--draggable,
  .tablehead__cell--nested {
    padding:0;
  }

  /* Thumbnails */
  .tablehead__cell--thumb,
  .tablehead__cell--icon,
  .tablehead__cell--draggable,
  .tablehead__cell--nested,
  .tablehead__cell--bulk {
    width:1px;

    .tablehead__arrow {
      display:none;
    }
  }

  .tablehead__cell--draggable {
    width:10px;
  }

  .tablehead__cell--bulk {
    width:15px + 20px;
  }

  .tablehead__cell--thumb {
    width:80px + 20px;

    @include breakpoint(xsmall) { // no thumbnail on smaller screens
      width:1px;
      padding-left:0;
      padding-right:0;
    }
  }

  .tablehead__cell--icon {
    width:20px + 20px;
  }

  .tablehead__cell--bulk {
    border-left: 1px solid transparent;
    padding-left:10px;
    padding-right:10px;

    a,
    .checkbox {
      display:block;
      width: 15px;
    }

    &:first-child {
      padding-left:20px;
    }
  }

  .tablehead__cell--sortable {
    cursor:pointer;

    &:hover .tablehead__arrow {
      opacity:1;
    }

    &.tablehead__cell--sorted .tablehead__arrow {
      opacity:1;
    }
  }

  .tablehead__cell--sorteddesc .tablehead__arrow {
    transform:rotate(180deg);
  }
</style>
