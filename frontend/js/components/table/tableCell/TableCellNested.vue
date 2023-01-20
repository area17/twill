<template>
  <span class="tablecell__nested-depth"
        :style="cellWidth">
  </span>
</template>

<script>
  import TableCellMixin from '@/mixins/tableCell'

  export default {
    name: 'A17TableCellNested',
    mixins: [TableCellMixin],
    props: {
      depth: {
        type: Number,
        default: 0
      },
      offset: {
        type: Number,
        default: 0
      }
    },
    computed: {
      cellWidth () {
        return this.depth > 0 ? { width: this.depth * 80 - 20 - this.offset + 'px' } : ''
      }
    }
  }
</script>

<style lang="scss" scoped>

  .tablecell__nested-depth {
    position: relative;
    display: block;
    height: 100%;

    &:before {
      content: '';
      position: absolute;
      top: 5px;
      left: 10px;
      width: 1px;
      height: 7px;
      background-color: $color__border--hover;
    }

    &:after {
      content: '';
      position: absolute;
      top: 12px;
      left: 10px;
      right: 0;
      height: 1px;
      background-color: $color__border--hover;
    }
  }

</style>
