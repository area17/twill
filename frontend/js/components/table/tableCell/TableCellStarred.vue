<template>
    <span v-if="row.hasOwnProperty('starred')" v-svg :symbol="[ row[colName] ? 'star-feature_active' : 'star-feature']"
      class="tablecell__starstate"
      :class="{'tablecell__starstate--live': row[colName]}"
      @click.prevent="toggleStarred"
      v-tooltip
      :data-tooltip-title="row.starred ? 'Unstar' : 'Star'">
    </span>
  </template>
  
  <script>
  import TableCellMixin from '@/mixins/tableCell'
  
  export default {
    name: 'A17TableCellStarred',
    mixins: [TableCellMixin],
    methods: {
      toggleStarred: function () {
        this.update()
      }
    }
  }
  </script>
  
  <style lang="scss" scoped>
  
    /* Publish/Unpublish content */
    .tablecell__starstate {
      cursor: pointer;
      transition: background-color 0.3s ease, border-color 0.3s ease;
    }
  
    .tablecell__starstate--live {
      color: $color__red;
    }
  
  </style>
  