<template>
  <a17-locale type="a17-textfield" :attributes="{ label: 'Quote', name: name, type: 'textarea', maxlength: 250, inStore: 'value' }"></a17-locale>
</template>

<script>
  export default {
    name: 'A17Quote',
    props: {
      name: {
        type: String,
        required: true
      }
    }
  }
</script>
