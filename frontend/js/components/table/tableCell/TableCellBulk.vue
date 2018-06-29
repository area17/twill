<template>
  <a href="#"
     @click.prevent.stop="toggleBulk">
    <a17-checkbox
      name="bulkEdit"
      :value="value"
      :initialValue="initialValue"/>
  </a>
</template>

<script>
  import { TableCellMixin } from '@/mixins'

  export default {
    name: 'A17TableCellBulk',
    mixins: [TableCellMixin],
    components: {},
    props: {
      value: {
        type: Number,
        required: true
      },
      initialValue: {
        type: Array,
        required: true
      }
    },
    methods: {
      toggleBulk: function () {
        this.update()
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';
  /* Bulk Edit checkboxes */
  a,
  .checkbox {
    display: block;
    width: 15px;
  }
</style>
