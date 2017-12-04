<template>
  <div class="block__body">
    <a17-mediafield :name="fieldName('image')" crop-context="image">Minimum image width 1300px</a17-mediafield>
  </div>
</template>

<script>
  import BlockMixin from '@/mixins/block'

  export default {
    name: 'A17Image',
    mixins: [BlockMixin]
  }
</script>
