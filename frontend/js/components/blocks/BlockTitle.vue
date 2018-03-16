<template>
  <div class="block__body">
    <a17-locale type="a17-textfield" :attributes="{ label: 'Title', name: fieldName('text'), type: 'text', maxlength: 250, inStore: 'value' }"></a17-locale>
  </div>
</template>

<script>
  import BlockMixin from '@/mixins/block'

  export default {
    name: 'A17Title',
    mixins: [BlockMixin]
  }
</script>
