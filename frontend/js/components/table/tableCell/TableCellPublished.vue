<template>
  <span v-if="row.hasOwnProperty('published')"
    class="tablecell__pubstate"
    :class="{'tablecell__pubstate--live': row[colName]}"
    @click.prevent="togglePublish"
    v-tooltip
    :data-tooltip-title="row.published ? 'Unpublish' : 'Publish'">
  </span>
</template>

<script>
  import TableCellMixin from '@/mixins/tableCell'

  export default {
    name: 'A17TableCellPublished',
    mixins: [TableCellMixin],
    methods: {
      togglePublish: function () {
        this.update()
      }
    }
  }
</script>

<style lang="scss" scoped>

  /* Publish/Unpublish content */
  .tablecell__pubstate {
    cursor: pointer;
    border-radius: 50%;
    height: 10px;
    width: 10px;
    display: block;
    background: $color__fborder;
    position: relative;
    top: 5px;
    transition: background-color 0.3s ease, border-color 0.3s ease;
  }

  .tablecell__pubstate--live {
    background: $color__publish;
  }

</style>
