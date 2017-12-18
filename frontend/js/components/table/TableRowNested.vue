<template>
  <tr class="nested">
    <td :colspan="tdWidth">
      <table class="nested__table">
        <template v-if="draggable">
          <draggable :element="'tbody'" v-model="rows" :options="draggableOptions" class="nested__dragArea" @start="startDrag"
                     @end="endDrag">
            <template v-for="(row, index) in rows">
              <a17-tablerow :rowType="'nested'" :row="row" :index="index" :key="row.id" :columns="columns" :nestedDepth="depth"></a17-tablerow>

              <a17-tablerow-nested v-if="depth <= maxDepth" :rowType="'nested'" :depth="depth + 1" :maxDepth="maxDepth" :parentId="row.id"
                                   :items="row.child" :columns="columns" :draggableOptions="draggableOptions"></a17-tablerow-nested>
            </template>
          </draggable>
        </template>
        <template v-else>
          <tbody class="tablerow-nested__body">
          <template v-for="(row, index) in rows">
            <a17-tablerow :rowType="'nested'" :row="row" :index="index" :key="row.id" :columns="columns" :nestedDepth="depth"></a17-tablerow>
            <a17-tablerow-nested v-if="depth <= maxDepth" :depth="depth + 1" :maxDepth="maxDepth" :parentId="row.id" :items="row.child" :columns="columns"></a17-tablerow-nested>
          </template>
          </tbody>
        </template>
      </table>
    </td>
  </tr>
</template>

<script>
  import A17TableRow from './TableRow'
  import draggableMixin from '@/mixins/draggable'
  import draggable from 'vuedraggable'
  import { EventBus, Events } from '@/utils/event-bus'

  export default {
    name: 'a17-tablerow-nested',
    components: {
      'a17-tablerow': A17TableRow,
      draggable
    },
    mixins: [draggableMixin],
    props: {
      parentId: {
        type: Number,
        required: true
      },
      items: {
        type: Array,
        default: function () {
          return []
        }
      },
      columns: {
        type: Array,
        default: function () {
          return []
        }
      },
      draggableOptions: {
        type: Object,
        default: () => {
        }
      },
      depth: {
        type: Number,
        default: 1
      },
      maxDepth: {
        type: Number,
        default: 1
      }
    },
    computed: {
      draggable () {
        return this.columns.find((col) => col.name === 'draggable')
      },
      rows: {
        get () {
          return this.items
        },
        set (value) {
          const data = {
            parentId: this.parentId,
            val: value
          }
          this.$store.dispatch('setDatatableNestedDatas', data)
        }
      },
      tdWidth: function () {
        // 2 come from the two last td in a17-tablerow component
        return this.columns.length + 2
      }
    },
    methods: {
      startDrag () { EventBus.$emit(Events.drag.start) },
      endDrag () { EventBus.$emit(Events.drag.end) }
    }
  }
</script>


<style lang="scss" scoped>
  .nested {
    width: 100%;
  }

  .nested__table {
    width: 100%;
    border-left: 80px solid transparent;
  }

  .nested__row {
    display: table;
    width: 100%;
  }

  .nested__dragArea {
    display: table;
    position: relative;
    width: 100%;
    min-height: 0px;
    transition: min-height 250ms ease;
    &.active {
      min-height: 50px;
    }
  }

</style>
