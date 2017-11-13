<template>
  <div class="block__body">
    <a17-repeater id="gridItem" :name="repeaterName('item')"></a17-repeater>
  </div>
</template>

<script>
  import BlockMixin from '@/mixins/block'

  export default {
    name: 'A17BlockGrid',
    mixins: [BlockMixin]
  }
</script>
