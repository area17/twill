<template>
  <div class="block__body">
    <a17-wysiwyg :name="fieldName('html')" inStore="value"></a17-wysiwyg>
  </div>
</template>

<script>
  import BlockMixin from '@/mixins/block'

  export default {
    name: 'A17Bodytext',
    mixins: [BlockMixin]
  }
</script>
