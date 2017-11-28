<template>
  <div class="block__body">
    <a17-repeater type="gridItem" :name="repeaterName('item')">Add up to 4 items</a17-repeater>
  </div>
</template>

<script>
  import BlockMixin from '@/mixins/block'

  export default {
    name: 'A17BlockGrid',
    mixins: [BlockMixin]
  }
</script>
