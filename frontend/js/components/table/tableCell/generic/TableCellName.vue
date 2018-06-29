<template>
  <a :href="editUrl"
     v-if="!row.hasOwnProperty('deleted')"
     class="tablecell__name"
     @click="preventEditInPlace($event)">
    <span class="f--link-underlined--o">{{ row[colName] }}</span>
  </a>
  <span v-else>{{ row[colName] }}</span>
</template>

<script>
  import { TableCellMixin } from '@/mixins'

  export default {
    name: 'A1TableCellName',
    mixins: [TableCellMixin]
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  /* Name */
  .tablecell__name {
    min-width: 15vw;
    max-width: 33.33vw;
    color: $color__link;
    text-decoration: none;
    display: block;
  }
</style>
