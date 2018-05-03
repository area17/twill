<template>
  <span v-if="row.hasOwnProperty('featured')"
        class="tablecell__feature"
        :class="{'tablecell__feature--active': row[colName] }"
        @click.prevent="toggleFeatured"
        v-tooltip
        :data-tooltip-title="row.featured ? 'Unfeature' : 'Feature'">
    <span v-svg
          symbol="star-feature_active">
    </span>
    <span v-svg
          symbol="star-feature">
    </span>
  </span>
</template>

<script>
  import { TableCellMixin } from '@/mixins'

  export default {
    name: 'A17TableCellFeatured',
    mixins: [TableCellMixin],
    methods: {
      toggleFeatured: function () {
        this.update()
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  /* Featuring content */
  .tablecell__feature {
    display: block;
    cursor: pointer;
    position: relative;
    top: 2px;

    .icon {
      color: $color__icons;
      display: block;
      top: -2px;
      position: relative;
    }

    .icon--star-feature_active {
      color: $color__error;
    }

    .icon--star-feature {
      display: block;
    }

    .icon--star-feature_active {
      display: none;
    }
  }

  .tablecell__feature--active {
    .icon svg {
      fill: $color__red;
    }

    .icon--star-feature {
      display: none;
    }

    .icon--star-feature_active {
      display: block;
    }
  }
</style>
