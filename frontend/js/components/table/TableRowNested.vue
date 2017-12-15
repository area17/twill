<template>
  <tr class="tablerow-nested">
    <td :colspan="tdWidth">
      <table class="tablerow-nested__table">
        <template v-if="draggable">
          <draggable :element="'tbody'" v-model="rows" :options="draggableOptions" class="tablerow-nested__dragArea tablerow-nested__body">
            <template v-for="(row, index) in rows">

              <a17-tablerow class="tablerow-nested__row" :row="row" :index="index" :key="row.id" :columns="columns"
                            :nested="nested" :draggable="draggable"></a17-tablerow>

              <a17-tablerow-nested v-if="depth < maxDepth" :depth="depth + 1" :maxDepth="maxDepth" :parentId="row.id"
                                   :items="row.child" :columns="columns" :draggable="draggable"
                                   :draggableOptions="draggableOptions"></a17-tablerow-nested>

            </template>
          </draggable>
        </template>

        <template v-else>
          <tbody class="tablerow-nested__body">
          <template v-for="(row, index) in rows">
            <a17-tablerow class="tablerow-nested__row" :row="row" :index="index" :key="row.id" :columns="columns"
                          :nested="nested" :draggable="draggable"></a17-tablerow>
            <a17-tablerow-nested v-if="depth < maxDepth" :depth="depth + 1" :maxDepth="maxDepth" :parentId="row.id"
                                 :items="row.child" :columns="columns"></a17-tablerow-nested>

          </template>
          </tbody>
        </template>
      </table>
    </td>
  </tr>
</template>

<script>
  /* eslint-disable */

  import A17TableRow from './TableRow'
  import draggableMixin from '@/mixins/draggable'
  import draggable from 'vuedraggable'

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
      draggable: {
        type: Boolean,
        default: false
      },
      draggableOptions: {
        type: Object,
        default: () => {
        }
      },
      reorderable: {
        type: Boolean,
        default: true
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
      rows: {
        get() {
          return this.items
        },
        set(value) {
          const data = {
            parentId: this.parentId,
            val: value
          }
          this.$store.dispatch('setDatatableNestedDatas', data)
        }
      },
      nested() {
        return {
          active: true,
          depth: this.depth
        }
      },
      tdWidth: function () {
        // 2 come from the two last td in a17-tablerow component
        return this.columns.length + 2
      }
    },
    methods: {
      checkMove(evt, original) {
        this.$emit('checkMove', evt, original)
      }
    }
  }
</script>


<style lang="scss" scoped>
  .tablerow-nested {

  }

  .tablerow-nested__table {
    width: 100%;
  }

  .tablerow-nested__dragArea {
    position: relative;
  }

  .tablerow-nested__body {
    position: relative;
    border-left: 80px solid transparent;
  }

  .tablerow-nested__row {
    display: table;
    width: 100%;
  }
</style>
