<template>
  <tr class="nested">
    <td :colspan="tdWidth">
      <table class="nested__table nested__table--parent">
        <template v-if="draggable">
          <draggable :element="'tbody'" v-model="rows" :options="draggableOptions" class="nested__dropArea" @start="onStart" @end="onEnd">
            <template v-for="(row, index) in rows">
              <tr class="nested">
                <td :colspan="tdWidth">
                  <table class="nested__table">
                    <a17-tablerow :rowType="'nested'" :row="row" :index="index" :key="row.id" :columns="columns" :nestedDepth="depth"></a17-tablerow>
                    <a17-tablerow-nested v-if="depth < maxDepth" :rowType="'nested'" :depth="depth + 1" :maxDepth="maxDepth" :parentId="row.id" :items="row.children" :columns="columns" :draggableOptions="draggableOptions"></a17-tablerow-nested>
                  </table>
                </td>
              </tr>
            </template>
          </draggable>
        </template>
        <template v-else>
          <tbody class="tablerow-nested__body">
            <template v-for="(row, index) in rows">
              <tr class="nested">
                <td :colspan="tdWidth">
                  <table class="nested__table">
                    <a17-tablerow :rowType="'nested'" :row="row" :index="index" :key="row.id" :columns="columns" :nestedDepth="depth"></a17-tablerow>
                    <a17-tablerow-nested v-if="depth < maxDepth" :rowType="'nested'" :depth="depth + 1" :maxDepth="maxDepth" :parentId="row.id" :items="row.children" :columns="columns" :draggableOptions="draggableOptions"></a17-tablerow-nested>
                  </table>
                </td>
              </tr>
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
  import nestedDraggableMixin from '@/mixins/nestedDraggable'
  import draggable from 'vuedraggable'

  export default {
    name: 'a17-tablerow-nested',
    components: {
      'a17-tablerow': A17TableRow,
      draggable
    },
    mixins: [draggableMixin, nestedDraggableMixin],
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
    }
  }
</script>


<style lang="scss" > // scoped
  @import '~styles/setup/_mixins-colors-vars.scss';

  .nested {
    width: 100%;
  }

  .nested__table {
    width: 100%;
    &.nested__table--parent {
      border-left: 80px solid transparent;
    }
  }

  .nested__row {
    display: table;
    width: 100%;
  }

  .nested__dropArea {
    display: table;
    position: relative;
    width: 100%;

    // Drop zone
    &::after {
      margin-top:-1px;
      position: relative;
      left: -240px;
      display: block;
      content: '';
      width: calc(100% + 240px);
      background-color: $color__background;
      border-bottom:1px solid $color__border--light;
      height:0;
      min-height: 1px;
      bottom: 0;
      z-index: 0;
      transition: min-height .3s linear;
    }
  }

  .nested__dropArea:empty {
    // Drop zone
    &::after {
      min-height: 1px;
    }
  }

</style>
