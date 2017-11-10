<template>
  <div class="block__body">
    <p>Demo BLOCK : A list of items inside a content editor block</p>
    <a17-repeater id="gridItem" :name="repeaterName('grid')"></a17-repeater>
  </div>
</template>

<script>
  import BlockMixin from '@/mixins/block'

  export default {
    name: 'A17BlockGrid',
    mixins: [BlockMixin]
  }
</script>
