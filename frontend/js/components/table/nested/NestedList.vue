<template>
  <draggable class="nested__dropArea"
             :class="nestedDropAreaClasses"
             v-model="rows"
             :options="draggableOptions"
             :element="'ul'"
             :component-data="draggableGetComponentData">
    <li class="nested-datatable__item"
        v-for="(row, index) in rows"
        :class="haveChildren(row.children)"
        :key="depth + '-' +  row.id">
      <a17-nested-item :index="index"
                       :row="row"
                       :columns="columns"/>
      <a17-nested-list v-if="row.children && depth < maxDepth"
                       :maxDepth="maxDepth"
                       :depth="depth + 1"
                       :parentId="row.id"
                       :items="row.children"
                       :nested="true"
                       :draggable="true"/>
    </li>
  </draggable>
</template>

<script>
  import { DATATABLE } from '@/store/mutations'
  import draggable from 'vuedraggable'
  import { DatatableMixin, DraggableMixin, NestedDraggableMixin } from '@/mixins/index'
  import NestedItem from './NestedItem'

  export default {
    name: 'a17-nested-list',
    components: {
      'a17-nested-item': NestedItem,
      draggable
    },
    mixins: [DatatableMixin, DraggableMixin, NestedDraggableMixin],
    props: {
      index: {
        type: Number,
        default: 0
      },
      items: {
        type: Array,
        default: () => []
      }
    },
    data: function () {
      return {
        handle: '.tablecell__handle'
      }
    },
    computed: {
      styleDepth: function () {
        return {
          'marginLeft': this.depth === 0 ? '0px' : '60px'
        }
      },
      rows: {
        get () {
          // return this.items
          return this.parentId > -1 ? this.items : this.$store.state.datatable.data
        },
        set (value) {
          const data = {
            parentId: this.parentId,
            val: value
          }

          const isChangingParents = (this.rows.length !== data.val.length)

          if (this.parentId > -1) {
            this.$store.commit(DATATABLE.UPDATE_DATATABLE_NESTED, data)
          } else {
            this.$store.commit(DATATABLE.UPDATE_DATATABLE_DATA, value)
          }
          this.saveNewTree(isChangingParents)
        }
      },
      nestedDropAreaClasses: function () {
        return [
          this.rows.length === 0 ? 'nested__dropArea--empty' : '',
          this.depth ? `nested__dropArea--depth nested__dropArea--depth${Math.min(10, this.depth)}` : ''
        ]
      },
      draggableOptions: function () {
        return {
          ...this.dragOptions,
          fallbackTolerance: 5,
          group: {
            name: this.name
          }
        }
      }
    },
    methods: {
      haveChildren: function (children) {
        return {
          'nested-datatable__item--empty': children.length === 0 && this.depth < this.maxDepth
        }
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .nested-datatable__item {
    &.sortable-ghost {
      opacity: 0.5;

      /deep/ > .nested-item {
        border: 10px solid $color__drag_bg--ghost;
      }
    }

    &.sortable-chosen {
      opacity: 0.5;
    }

    &.sortable-drag {
      display: block;
      opacity: 0.95!important;
    }

    &.nested-datatable__item--empty {
      /deep/ > .nested-item {
        margin-bottom: 0;
      }
    }
  }

  .nested__dropArea {

    * {
      will-change: auto;
    }

    &.nested__dropArea--empty {
      padding-top: 10px;
      min-height: 10px;

      /deep/ .nested-item {
        margin-bottom: 0;
      }
    }
  }

  .nested__dropArea--depth > li > div {
    &::after {
      content:'';
      display:block;
      height:6px;
      border-left:1px solid #D9D9D9;
      border-bottom:1px solid #D9D9D9;
      position:absolute;
      top:calc(50% - 3px);
      left:20px;
      background-color: transparent;
      width:0;
      pointer-events:none;
    }
  }

  @for $i from 1 through 10 {
    .nested__dropArea--depth#{$i} > li > div {
      padding-left:#{$i * 50px};

      &::after {
        width:#{($i * 50px) - 20px};
      }
    }
  }

</style>
