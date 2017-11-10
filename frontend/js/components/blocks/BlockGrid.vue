<template>
  <div class="block__body">
    <p>A grid of videos inside a content editor</p>
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
