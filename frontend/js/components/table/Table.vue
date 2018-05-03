<template>
  <div class="table__scroller" @scroll="updateScroll">
    <table class="table" :class="{'table--sized' : columnsWidth.length }">
      <colgroup v-if="columnsWidth.length">
        <col v-for="(width, index) in columnsWidth" :style="colWidths[index]" />
      </colgroup>
      <slot>
      </slot>
    </table>
  </div>
</template>

<script>
  export default {
    name: 'A17Table',
    props: {
      xScroll: {
        type: Number,
        default: 1
      },
      columnsWidth: {
        type: Array,
        default: function () { return [] }
      }
    },
    data: function () {
      return {
        currentScroll: this.xScroll
      }
    },
    computed: {
      colWidths: function () {
        return this.columnsWidth.map(function (width) {
          return { 'width': width ? width + 'px' : '' }
        })
      }
    },
    watch: {
      xScroll: function (value) {
        if (this.currentScroll !== value) {
          this.currentScroll = value
          this.$el.scrollLeft = value // scroll the table horizontally
        }
      }
    },
    methods: {
      updateScroll: function () {
        const newValue = this.$el.scrollLeft

        if (this.currentScroll !== newValue) {
          this.currentScroll = newValue
          this.$emit('scroll', newValue)
        }
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .table__scroller {
    width:100%;
    overflow:hidden;
    overflow-x: auto;
  }

  .table {
    overflow: hidden;
    width: 100%;
    border-collapse: collapse;
    border-spacing: 0;

    &.table--nested {
      background-color: $color__drag_bg--ghost;
    }
  }

  .table--sized {
    table-layout: fixed;
  }

  .table__spacer {
    width:50px;
  }
</style>
