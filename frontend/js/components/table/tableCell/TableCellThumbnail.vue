<template>
  <div :class="getThumbClasses">
    <a
        :href="!row.hasOwnProperty('deleted') ? editUrl : false"
        @click="!row.hasOwnProperty('deleted') ? preventEditInPlace($event) : null"
    >
      <template v-if="col.variation === 'rounded'">
        <a17-avatar
            :name="rowTitle"
            :thumbnail="row[colName]"
        />
      </template>
      <template v-else>
        <img :src="row[colName]"/>
      </template>
    </a>
  </div>
</template>

<script>
  import A17Avatar from '@/components/Avatar.vue'
  import TableCellMixin from '@/mixins/tableCell'

  export default {
    name: 'A17TableCellThumbNail',
    mixins: [TableCellMixin],
    components: {
      'a17-avatar': A17Avatar
    },
    computed: {
      rowTitle() {
        return this.row.name ?? this.row.title.replace(/<[^>]*>?/gm, '') ?? ''
      },
      getThumbClasses () {
        return [
          'tablecell__thumb',
          this.col.variation ? `tablecell__thumb--${this.col.variation}` : ''
        ]
      }
    }
  }
</script>

<style lang="scss" scoped>

  .tablecell--thumb {
    width: 1px;

    @include breakpoint(xsmall) { // no thumbnail on smaller screens
      padding-left: 0;
      padding-right: 0;
    }
  }

  .tablecell__thumb {
    float: left;
    display: block;
    background: $color__border--light;

    @include breakpoint(xsmall) { // no thumbnail on smaller screens
      display: none;
    }

    a {
      display: block;
      position: relative;
      width: 100%;
      height: 100%;
    }

    img {
      display: block;
      width: 80px;
      min-height: 80px;
      height: auto;
    }
  }

  /* Modifiers */

  .tablecell__thumb--rounded {
    position: relative;
    width: 36px;
    height: 36px;
    margin: -8px 0;
    border-radius: 50%;
  }
</style>
