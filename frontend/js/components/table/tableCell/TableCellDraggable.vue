<template functional>
  <span class="tablecell__handle"></span>
</template>

<script>
  import TableCellMixin from '@/mixins/tableCell'

  export default {
    name: 'A17TableCellDraggable',
    mixins: [TableCellMixin]
  }
</script>

<style lang="scss" scoped>

  .tablecell__handle {
    display: none;
    position: absolute;
    height: 40px;
    width: 10px;
    left: 50%;
    top: 50%;
    margin-left: -5px;
    margin-top: -20px;
    @include dragGrid($color__drag, $color__f--bg);

    tr:hover > .tablecell--draggable & {
      display: block;
    }
  }
</style>
