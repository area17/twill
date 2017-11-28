<template>
  <div class="block__body">
    <a17-locale type="a17-textfield" :attributes="{ label: 'Title', name: fieldName('title'), type: 'text', maxlength: 250, inStore: 'value' }"></a17-locale>
    <a17-locale type="a17-textfield" :attributes="{ label: 'Video URL', name: fieldName('url'), type: 'text', maxlength: 250, inStore: 'value' }"></a17-locale>
    <a17-inputframe label="Thumbnail"><a17-mediafield :name="fieldName('thumbnail')" cropContext="thumbnail" ref="thumbnail">Minimum image width 1300px</a17-mediafield></a17-inputframe>
  </div>
</template>

<script>
  // Demo Video Block

  // This block can used to create other blocks in your Application
  // This show how you can easily create complex blocks that are connected with the media library or any related content
  import BlockMixin from '@/mixins/block'

  export default {
    name: 'A17Video',
    mixins: [BlockMixin]
  }
</script>
