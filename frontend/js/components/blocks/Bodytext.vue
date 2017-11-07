<template>
  <a17-wysiwyg :name="name" inStore="value"></a17-wysiwyg>
</template>

<script>
  export default {
    name: 'A17Bodytext',
    props: {
      name: {
        type: String,
        required: true
      }
    }
  }
</script>
