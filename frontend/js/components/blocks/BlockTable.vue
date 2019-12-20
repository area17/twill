<template>
  <div class="block__body">
    <a17-wysiwyg-tiptap :name="fieldName('html')" label="Body" :options='{"modules":{"toolbar":["table"],"syntax":false}}' placeholder='Insert your table' editSource="true" inStore="value"></a17-wysiwyg-tiptap>
  </div>
</template>

<script>
  import BlockMixin from '@/mixins/block'

  export default {
    name: 'A17Bodytable',
    mixins: [BlockMixin]
  }
</script>
