<template functional>
  <span :class="`icon icon--wysiwyg_${props.icon}`"
        aria-hidden="true">
    <svg>
      <title>{{ props.icon }}</title>
      <use :xlink:href="`#wysiwyg_${props.icon}`"></use>
    </svg>
  </span>
</template>

<script>
  export default {
    name: 'WysiwygIcon',
    props: {
      icon: {
        type: String,
        required: true
      }
    }
  }
</script>
